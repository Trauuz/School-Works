<?php

namespace App\Http\Controllers;

use App\Services\WeatherForecast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __invoke(Request $request, WeatherForecast $weather): JsonResponse
    {
        return response()->json($weather->for($request->user()))->header('Cache-Control', 'private, no-store');
    }
}
