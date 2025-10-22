<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TimezoneController extends Controller
{
    public function setTimezone(Request $request): JsonResponse
    {
        $request->validate([
            'timezone' => 'required|string|max:100'
        ]);

        $timezone = $request->input('timezone');
        
        // Validate timezone
        if (!in_array($timezone, timezone_identifiers_list())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid timezone provided'
            ], 422);
        }

        // Store timezone in session
        session(['timezone' => $timezone]);
        
        return response()->json([
            'success' => true,
            'timezone' => $timezone,
            'message' => 'Timezone updated successfully'
        ]);
    }
}