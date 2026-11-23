<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class LocationSearch
{
    /** @return array{address_line: string, city: string, region: string, country_code: string, postal_code: string} */
    public function reverse(float $latitude, float $longitude): array
    {
        $coordinates = ['lat' => round($latitude, 7), 'lon' => round($longitude, 7)];

        return Cache::remember('address:'.hash('sha256', json_encode($coordinates)), now()->addDay(), function () use ($coordinates): array {
            try {
                $response = Http::acceptJson()->connectTimeout(3)->timeout(8)
                    ->get(rtrim(config('services.photon.url'), '/').'/reverse', [...$coordinates, 'limit' => 1, 'radius' => 1, 'lang' => 'en']);
            } catch (ConnectionException) {
                throw ValidationException::withMessages(['address' => 'Address lookup is unavailable. You can enter your address manually.']);
            }

            $address = $response->json('features.0.properties');
            if (! $response->successful() || ! is_array($address) || empty($address['countrycode'])) {
                throw ValidationException::withMessages(['address' => 'No address was found for this pin. You can enter your address manually.']);
            }

            $text = fn (string $key): string => is_string($address[$key] ?? null) ? trim($address[$key]) : '';
            $street = trim($text('housenumber').' '.$text('street'));

            return [
                'address_line' => mb_substr(implode(', ', array_unique(array_filter([$street, $text('district') ?: $text('locality')]))), 0, 255),
                'city' => mb_substr($text('city') ?: $text('county'), 0, 100),
                'region' => mb_substr($text('state') ?: $text('county'), 0, 100),
                'country_code' => strtoupper($text('countrycode')),
                'postal_code' => mb_substr($text('postcode'), 0, 20),
            ];
        });
    }

    /** @return list<array{id: int, name: string, admin1: string, country_code: string, latitude: float, longitude: float, timezone: string}> */
    public function search(string $query, string $country): array
    {
        return Cache::remember('cities:'.hash('sha256', mb_strtolower($query).$country), now()->addDay(), function () use ($query, $country): array {
            $data = $this->fetch('search', ['name' => $query, 'countryCode' => $country, 'count' => 8, 'language' => 'en']);

            return array_values(array_map($this->normalize(...), $data['results'] ?? []));
        });
    }

    /** @return array{id: int, name: string, admin1: string, country_code: string, latitude: float, longitude: float, timezone: string} */
    public function find(int $id): array
    {
        return Cache::remember('city:'.$id, now()->addDay(), fn (): array => $this->normalize($this->fetch('get', ['id' => $id])));
    }

    /** @param array<string, mixed> $parameters @return array<string, mixed> */
    private function fetch(string $path, array $parameters): array
    {
        try {
            $response = Http::acceptJson()->connectTimeout(3)->timeout(5)->get('https://geocoding-api.open-meteo.com/v1/'.$path, $parameters);
        } catch (ConnectionException) {
            throw ValidationException::withMessages(['weather_location_id' => 'City lookup is unavailable. Clear the weather city to continue with your manual address.']);
        }
        if (! $response->successful() || ! is_array($response->json()) || $response->json('error')) {
            throw ValidationException::withMessages(['weather_location_id' => 'City lookup is unavailable. Clear the weather city to continue with your manual address.']);
        }

        return $response->json();
    }

    /** @param array<string, mixed> $data @return array{id: int, name: string, admin1: string, country_code: string, latitude: float, longitude: float, timezone: string} */
    private function normalize(array $data): array
    {
        if (! isset($data['id'], $data['name'], $data['latitude'], $data['longitude'], $data['timezone'], $data['country_code'])) {
            throw ValidationException::withMessages(['weather_location_id' => 'That city could not be found. Choose another city or clear the selection.']);
        }

        if (! in_array($data['timezone'], \DateTimeZone::listIdentifiers(), true)) {
            throw ValidationException::withMessages(['weather_location_id' => 'That city has an invalid timezone. Choose another city or clear the selection.']);
        }

        return ['id' => (int) $data['id'], 'name' => $data['name'], 'admin1' => $data['admin1'] ?? '', 'country_code' => $data['country_code'], 'latitude' => (float) $data['latitude'], 'longitude' => (float) $data['longitude'], 'timezone' => $data['timezone']];
    }
}
