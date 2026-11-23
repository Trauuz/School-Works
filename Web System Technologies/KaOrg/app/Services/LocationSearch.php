<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class LocationSearch
{
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
