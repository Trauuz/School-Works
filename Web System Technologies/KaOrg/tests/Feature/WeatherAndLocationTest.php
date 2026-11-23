<?php

use App\Http\Middleware\EnsureSupabaseSession;
use App\Models\User;
use App\Services\LocationSearch;
use App\Services\WeatherForecast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

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

test('forecasts are cached by city rather than precise address', function () {
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
    expect($weather->for(User::factory()->create())['message'])->toContain('Add your city');
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
