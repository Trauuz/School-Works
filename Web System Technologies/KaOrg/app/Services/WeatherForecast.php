<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherForecast
{
    /** @return array{message: string, condition: ?string, city: ?string, updated_at: ?string} */
    public function for(User $user): array
    {
        $hasPin = $user->address_latitude !== null && $user->address_longitude !== null;
        $latitude = $hasPin ? $user->address_latitude : $user->weather_latitude;
        $longitude = $hasPin ? $user->address_longitude : $user->weather_longitude;
        $city = $hasPin ? $user->city : $user->weather_city;
        if ($latitude === null || $longitude === null) {
            return ['message' => 'Place an address pin to see local weather.', 'condition' => null, 'city' => null, 'updated_at' => null];
        }
        try {
            $forecast = Cache::remember('weather:conditions:v1:'.round((float) $latitude, 4).':'.round((float) $longitude, 4), now()->addMinutes(30), function () use ($latitude, $longitude, $user): array {
                $response = Http::acceptJson()->connectTimeout(3)->timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $latitude, 'longitude' => $longitude, 'timezone' => 'auto',
                    'current' => 'weather_code,is_day,apparent_temperature,precipitation', 'hourly' => 'weather_code,precipitation_probability', 'forecast_days' => 2,
                ]);
                if (! $response->successful()) {
                    throw new \UnexpectedValueException;
                }
                $data = $response->json();
                if (! is_array($data) || ! isset($data['current']['weather_code'], $data['current']['apparent_temperature'], $data['current']['precipitation'], $data['current']['time'], $data['hourly']['time'], $data['hourly']['weather_code'], $data['hourly']['precipitation_probability'])) {
                    throw new \UnexpectedValueException;
                }
                foreach (['time', 'weather_code', 'precipitation_probability'] as $field) {
                    if (! is_array($data['hourly'][$field])) {
                        throw new \UnexpectedValueException;
                    }
                }
                $timezone = $data['timezone'] ?? $user->weather_timezone ?? 'UTC';
                if (! is_string($timezone) || ! in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
                    throw new \UnexpectedValueException;
                }
                $now = CarbonImmutable::now($timezone);
                $codes = [(int) $data['current']['weather_code']];
                $probabilities = [];
                foreach ($data['hourly']['time'] as $index => $time) {
                    $hour = CarbonImmutable::parse($time, $timezone);
                    if ($hour->gte($now) && $hour->lte($now->addHours(6))) {
                        if (! isset($data['hourly']['weather_code'][$index], $data['hourly']['precipitation_probability'][$index])) {
                            throw new \UnexpectedValueException;
                        }
                        $codes[] = (int) $data['hourly']['weather_code'][$index];
                        $probabilities[] = (int) $data['hourly']['precipitation_probability'][$index];
                    }
                }
                $message = $this->message($codes, $probabilities, (float) $data['current']['apparent_temperature'], (float) $data['current']['precipitation']);

                return ['message' => $message, 'condition' => $this->condition((int) $data['current']['weather_code'], (int) ($data['current']['is_day'] ?? 1) === 1), 'city' => null, 'updated_at' => CarbonImmutable::parse($data['current']['time'], $timezone)->toIso8601String()];
            });

            return [...$forecast, 'city' => $city];
        } catch (ConnectionException|\UnexpectedValueException|InvalidFormatException) {
            return ['message' => 'Weather is temporarily unavailable.', 'condition' => null, 'city' => $city, 'updated_at' => null];
        }
    }

    private function condition(int $code, bool $isDay): string
    {
        return match ($code) {
            0 => $isDay ? 'Sunny' : 'Clear night',
            1 => $isDay ? 'Mostly sunny' : 'Mostly clear',
            2 => 'Partly cloudy',
            3 => 'Cloudy',
            45, 48 => 'Foggy',
            51, 53, 55 => 'Drizzly',
            56, 57 => 'Freezing drizzle',
            61, 63, 65, 80, 81, 82 => 'Rainy',
            66, 67 => 'Freezing rain',
            71, 73, 75, 77, 85, 86 => 'Snowy',
            95, 97 => 'Thunderstorms',
            96, 99 => 'Thunderstorms with hail',
            default => 'Conditions unavailable',
        };
    }

    /** @param list<int> $codes @param list<int> $probabilities */
    private function message(array $codes, array $probabilities, float $feelsLike, float $precipitation): string
    {
        if (array_intersect($codes, [95, 96, 99])) {
            return 'Thunderstorms are forecast nearby. Take care outdoors and seek shelter if thunder starts.';
        }
        if ($precipitation > 0 || in_array($codes[0], [51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82], true) || max([0, ...$probabilities]) >= 60) {
            return 'Rain is expected nearby in the next few hours. Bring an umbrella when heading out.';
        }
        if ($feelsLike >= 32) {
            return 'It feels hot outside. Stay hydrated and take breaks in the shade.';
        }
        if ($codes[0] === 0 || $codes[0] === 1) {
            return 'Mostly clear conditions nearby. Enjoy your day and stay hydrated.';
        }

        return 'Check the local conditions before heading out. Have a good day!';
    }
}
