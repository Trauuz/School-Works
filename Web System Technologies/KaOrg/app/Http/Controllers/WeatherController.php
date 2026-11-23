<?php

namespace App\Http\Controllers;

use App\Services\WeatherForecast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __invoke(Request $request, WeatherForecast $weather): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['required_with:latitude', 'numeric', 'between:-180,180'],
        ]);
        $user = clone $request->user();
        if (isset($data['latitude'], $data['longitude'])) {
            $user->forceFill(['address_latitude' => $data['latitude'], 'address_longitude' => $data['longitude'], 'city' => null]);
        }

        return response()->json($weather->for($user))->header('Cache-Control', 'private, no-store');
    }
}
