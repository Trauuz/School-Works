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
    /** @return array{message: string, city: ?string, updated_at: ?string} */
    public function for(User $user): array
    {
        if (! $user->weather_location_id) {
            return ['message' => 'Add your city to see local weather.', 'city' => null, 'updated_at' => null];
        }
        try {
            return Cache::remember('weather:'.$user->weather_location_id, now()->addMinutes(30), function () use ($user): array {
                $response = Http::acceptJson()->connectTimeout(3)->timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $user->weather_latitude, 'longitude' => $user->weather_longitude, 'timezone' => $user->weather_timezone,
                    'current' => 'weather_code,apparent_temperature,precipitation', 'hourly' => 'weather_code,precipitation_probability', 'forecast_days' => 2,
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
                $timezone = $user->weather_timezone;
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

                return ['message' => $message, 'city' => $user->weather_city, 'updated_at' => CarbonImmutable::parse($data['current']['time'], $timezone)->toIso8601String()];
            });
        } catch (ConnectionException|\UnexpectedValueException|InvalidFormatException) {
            return ['message' => 'Weather is temporarily unavailable.', 'city' => $user->weather_city, 'updated_at' => null];
        }
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
