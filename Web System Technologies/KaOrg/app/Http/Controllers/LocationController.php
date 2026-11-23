<?php

namespace App\Http\Controllers;

use App\Services\LocationSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __invoke(Request $request, LocationSearch $locations): JsonResponse
    {
        $data = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100'], 'country' => ['required', 'regex:/^[A-Z]{2}$/']]);

        return response()->json(['results' => $locations->search($data['q'], $data['country'])]);
    }
}
