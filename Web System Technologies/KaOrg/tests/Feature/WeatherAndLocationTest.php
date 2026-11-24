<?php

use App\Http\Middleware\EnsureSupabaseSession;
use App\Models\User;
use App\Services\LocationSearch;
use App\Services\WeatherForecast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);
beforeEach(function () {
    Http::preventStrayRequests();
});
function weatherUser(): User
{
    return User::factory()->create(['weather_location_id' => 1701668, 'weather_city' => 'Manila', 'weather_latitude' => 14.6, 'weather_longitude' => 120.98, 'weather_timezone' => 'Asia/Manila']);
}
function forecastResponse(int $code, int $probability, float $temperature = 28, float $precipitation = 0): array
{
    $time = now('Asia/Manila')->startOfHour();

    return ['current' => ['time' => $time->format('Y-m-d\TH:i'), 'weather_code' => $code, 'apparent_temperature' => $temperature, 'precipitation' => $precipitation], 'hourly' => ['time' => [$time->copy()->addHour()->format('Y-m-d\TH:i')], 'weather_code' => [$code], 'precipitation_probability' => [$probability]]];
}

test('weather messages follow storm rain heat and clear priority', function (int $code, int $probability, float $temperature, string $text) {
    Http::fake(['*' => Http::response(forecastResponse($code, $probability, $temperature))]);
    expect(app(WeatherForecast::class)->for(weatherUser())['message'])->toContain($text);
})->with([[95, 100, 36, 'Thunderstorms'], [61, 0, 36, 'umbrella'], [3, 60, 36, 'umbrella'], [3, 0, 32, 'hydrated'], [0, 0, 28, 'Mostly clear'], [3, 0, 28, 'local conditions']]);

test('forecasts share a cache for matching coordinates and expire after thirty minutes', function () {
    Http::fake(['*' => Http::response(forecastResponse(0, 0))]);
    $first = weatherUser();
    $second = weatherUser();
    $weather = app(WeatherForecast::class);
    $weather->for($first);
    $weather->for($second);
    Http::assertSentCount(1);
    $this->travel(31)->minutes();
    $weather->for($second);
    Http::assertSentCount(2);
});

test('missing location and malformed or unavailable forecasts have honest fallbacks', function () {
    $weather = app(WeatherForecast::class);
    expect($weather->for(User::factory()->create())['message'])->toContain('Place an address pin');
    Http::assertNothingSent();
    Http::fake(['*' => Http::response(['current' => []])]);
    expect($weather->for(weatherUser())['message'])->toBe('Weather is temporarily unavailable.');
    Http::fake(['*' => Http::response([], 503)]);
    expect($weather->for(weatherUser())['message'])->toBe('Weather is temporarily unavailable.');
});

test('city lookup returns disambiguating locations and validates chosen IDs server side', function () {
    $city = ['id' => 1701668, 'name' => 'Manila', 'admin1' => 'Metro Manila', 'country_code' => 'PH', 'latitude' => 14.6, 'longitude' => 120.98, 'timezone' => 'Asia/Manila'];
    Http::fake(['*/search*' => Http::response(['results' => [$city]]), '*/get*' => Http::response($city)]);
    $locations = app(LocationSearch::class);
    expect($locations->search('Manila', 'PH')[0]['admin1'])->toBe('Metro Manila')->and($locations->find(1701668)['latitude'])->toBe(14.6);
    Http::assertSentCount(2);
});

test('manual address can be saved without map or location provider calls', function () {
    $user = User::factory()->create();
    $this->withoutMiddleware(EnsureSupabaseSession::class)->actingAs($user)->put(route('account.profile.update'), ['address_line' => '12 Sample Street', 'city' => 'Manila', 'region' => 'Metro Manila', 'country_code' => 'PH'])->assertSessionHasNoErrors();
    expect($user->fresh()->city)->toBe('Manila')->and($user->fresh()->weather_location_id)->toBeNull();
    Http::assertNothingSent();
});

test('pin lookup returns address details and caches repeated coordinates', function () {
    Http::fake(['photon.komoot.io/reverse*' => Http::response(['features' => [['properties' => ['housenumber' => '12', 'street' => 'Sample Street', 'district' => 'Barangay One', 'city' => 'Manila', 'state' => 'Metro Manila', 'countrycode' => 'ph', 'postcode' => '1000']]]])]);
    $this->withoutMiddleware(EnsureSupabaseSession::class)->actingAs(User::factory()->create());
    $parameters = ['latitude' => 14.6, 'longitude' => 120.98];
    $expected = ['address_line' => '12 Sample Street, Barangay One', 'city' => 'Manila', 'region' => 'Metro Manila', 'country_code' => 'PH', 'postal_code' => '1000'];
    $this->getJson(route('locations.reverse', $parameters))->assertOk()->assertJsonPath('address', $expected);
    $this->getJson(route('locations.reverse', $parameters))->assertOk();
    Http::assertSentCount(1);
    Http::assertSent(fn ($request): bool => $request['lat'] == 14.6 && $request['lon'] == 120.98);
});

test('pin lookup requires authentication and valid coordinates', function () {
    $this->getJson(route('locations.reverse'))->assertUnauthorized();
    $this->withoutMiddleware(EnsureSupabaseSession::class)->actingAs(User::factory()->create())
        ->getJson(route('locations.reverse', ['latitude' => 91, 'longitude' => -181]))
        ->assertUnprocessable()->assertJsonValidationErrors(['latitude', 'longitude']);
    Http::assertNothingSent();
});

test('pin lookup handles sparse addresses without inventing street details', function () {
    Http::fake(['photon.komoot.io/reverse*' => Http::response(['features' => [['properties' => ['name' => 'A nearby shop', 'county' => 'Sample Municipality', 'countrycode' => 'ph']]]])]);
    expect(app(LocationSearch::class)->reverse(12.3, 121.4))->toBe(['address_line' => '', 'city' => 'Sample Municipality', 'region' => 'Sample Municipality', 'country_code' => 'PH', 'postal_code' => '']);
});

test('pin lookup fails gracefully when the provider cannot return an address', function (string $failure) {
    Http::fake(['photon.komoot.io/reverse*' => match ($failure) {
        'outage' => Http::response([], 503),
        'malformed' => Http::response('invalid json'),
        'connection' => Http::failedConnection(),
        default => Http::response(['features' => []]),
    }]);
    $this->withoutMiddleware(EnsureSupabaseSession::class)->actingAs(User::factory()->create())
        ->getJson(route('locations.reverse', ['latitude' => 14.6, 'longitude' => 120.98]))
        ->assertUnprocessable()->assertJsonValidationErrors('address');
})->with(['outage', 'malformed', 'connection', 'empty']);

test('saved address pins drive weather without selecting a city', function () {
    $user = User::factory()->create();
    $this->withoutMiddleware(EnsureSupabaseSession::class)->actingAs($user)->put(route('account.profile.update'), [
        'address_line' => '12 Sample Street', 'city' => 'Manila', 'region' => 'Metro Manila', 'country_code' => 'PH',
        'address_latitude' => 14.5896, 'address_longitude' => 120.9747,
    ])->assertSessionHasNoErrors();
    Http::assertNothingSent();
    Http::fake(['api.open-meteo.com/*' => Http::response([...forecastResponse(0, 0), 'timezone' => 'Asia/Manila'])]);
    $this->getJson(route('weather.show'))->assertOk()->assertJsonPath('city', 'Manila')->assertJsonPath('message', 'Mostly clear conditions nearby. Enjoy your day and stay hydrated.');
    expect($user->fresh()->weather_location_id)->toBeNull();
    Http::assertSent(fn ($request): bool => $request['latitude'] == 14.5896 && $request['longitude'] == 120.9747 && $request['timezone'] === 'auto');
});

test('weather previews use unsaved pin coordinates without changing the saved address', function () {
    $user = weatherUser();
    $user->forceFill(['address_latitude' => 14.6, 'address_longitude' => 120.98])->save();
    Http::fake(['api.open-meteo.com/*' => Http::response([...forecastResponse(0, 0), 'timezone' => 'Asia/Manila'])]);
    $this->withoutMiddleware(EnsureSupabaseSession::class)->actingAs($user);
    $this->getJson(route('weather.show'))->assertOk();
    $this->getJson(route('weather.show', ['latitude' => 0, 'longitude' => 0]))->assertOk();
    Http::assertSentCount(2);
    Http::assertSent(fn ($request): bool => $request['latitude'] == 0 && $request['longitude'] == 0);
    expect((float) $user->fresh()->address_latitude)->toBe(14.6)->and((float) $user->fresh()->address_longitude)->toBe(120.98);
});

test('weather preview validates both coordinates and is available during profile setup', function () {
    $user = User::factory()->create(['profile_completed_at' => null]);
    Http::fake(['api.open-meteo.com/*' => Http::response([...forecastResponse(0, 0), 'timezone' => 'Asia/Manila'])]);
    $this->withoutMiddleware(EnsureSupabaseSession::class)->actingAs($user);
    $this->getJson(route('weather.show', ['latitude' => 91, 'longitude' => 0]))->assertUnprocessable()->assertJsonValidationErrors('latitude');
    $this->getJson(route('weather.show', ['latitude' => 14]))->assertUnprocessable()->assertJsonValidationErrors('longitude');
    Http::assertNothingSent();
    $this->getJson(route('weather.show', ['latitude' => 14.6, 'longitude' => 120.98]))->assertOk();
});

test('address form keeps the map visible and weather below the pin without city controls', function () {
    $this->withoutMiddleware(EnsureSupabaseSession::class)->actingAs(User::factory()->create())
        ->get(route('account.profile'))->assertOk()
        ->assertSeeInOrder(['id="address-pin-heading"', 'data-address-map', 'id="pin-weather-heading"'], false)
        ->assertDontSee('<details class="map-details">', false)->assertDontSee('Find city for weather')->assertDontSee('City search by');
});

test('weather labels describe current conditions including clear nights', function (int $code, bool $isDay, string $condition) {
    $forecast = forecastResponse($code, 0);
    $forecast['current']['is_day'] = $isDay ? 1 : 0;
    Http::fake(['api.open-meteo.com/*' => Http::response($forecast)]);
    expect(app(WeatherForecast::class)->for(weatherUser())['condition'])->toBe($condition);
})->with([
    [0, true, 'Sunny'], [0, false, 'Clear night'], [1, true, 'Mostly sunny'], [1, false, 'Mostly clear'],
    [2, true, 'Partly cloudy'], [3, true, 'Cloudy'], [45, true, 'Foggy'], [51, true, 'Drizzly'],
    [56, true, 'Freezing drizzle'], [61, true, 'Rainy'], [82, true, 'Rainy'], [66, true, 'Freezing rain'],
    [71, true, 'Snowy'], [95, true, 'Thunderstorms'], [97, true, 'Thunderstorms'], [99, true, 'Thunderstorms with hail'],
    [999, true, 'Conditions unavailable'],
]);

test('unavailable weather has no misleading condition label', function () {
    $weather = app(WeatherForecast::class);
    expect($weather->for(User::factory()->create())['condition'])->toBeNull();
    Http::fake(['api.open-meteo.com/*' => Http::response([], 503)]);
    expect($weather->for(weatherUser())['condition'])->toBeNull();
});

test('weather failures log actionable reasons without exposing the pinned coordinates', function (string $failure, string $reason, ?int $curlCode) {
    Log::spy();
    Http::fake(['api.open-meteo.com/*' => match ($failure) {
        'forbidden' => Http::response([], 403),
        'rate_limit' => Http::response([], 429),
        'malformed' => Http::response(['current' => []]),
        default => Http::failedConnection('cURL error 28: Operation timed out'),
    }]);
    expect(app(WeatherForecast::class)->for(weatherUser())['message'])->toBe('Weather is temporarily unavailable.');
    Log::shouldHaveReceived('warning')->once()->with('Weather lookup failed', ['provider' => 'open-meteo', 'reason' => $reason, 'curl_code' => $curlCode]);
})->with([
    ['forbidden', 'provider_http_403', null],
    ['rate_limit', 'provider_http_429', null],
    ['malformed', 'missing_forecast_fields', null],
    ['timeout', 'connection_failure', 28],
]);
