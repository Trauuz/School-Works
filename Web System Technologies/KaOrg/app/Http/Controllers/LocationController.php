<?php

namespace App\Http\Controllers;

use App\Services\LocationSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function reverse(Request $request, LocationSearch $locations): JsonResponse
    {
        $data = $request->validate(['latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180']]);

        return response()->json(['address' => $locations->reverse((float) $data['latitude'], (float) $data['longitude'])])->header('Cache-Control', 'private, no-store');
    }

    public function __invoke(Request $request, LocationSearch $locations): JsonResponse
    {
        $data = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100'], 'country' => ['required', 'regex:/^[A-Z]{2}$/']]);

        return response()->json(['results' => $locations->search($data['q'], $data['country'])]);
    }
}
