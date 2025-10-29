<?php

namespace App;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Closure;
use Exception;

trait HandleCrudResponses
{
    /**
     * Execute an action wrapped in try/catch and return redirect with flash.
     *
     * @param  Closure  $callback
     * @param  string   $successMessage
     * @param  string   $errorMessage
     * @param  string   $redirectRoute
     * @param  bool     $backOnError
     * @return RedirectResponse
     */
     protected function tryCatchResponse(
        Closure $callback,
        string $successMessage,
        string $errorMessage,
        string $redirectRoute,
        bool $backOnError = true
    ): RedirectResponse {
        try {
            $callback();

            return redirect()->route($redirectRoute)
                ->with('success', $successMessage);
        } catch (Exception $e) {
            if ($backOnError) {
                return redirect()->back()->withInput()
                    ->with('error', $errorMessage . ' ' . $e->getMessage());
            }

            return redirect()->route($redirectRoute)
                ->with('error', $errorMessage . ' ' . $e->getMessage());
        }
    }

    /**
     * Execute an action wrapped in try/catch and return JSON response.
     */
    protected function tryCatchJsonResponse(
        Closure $callback,
        string $errorMessage = 'Terjadi kesalahan'
    ): JsonResponse {
        try {
            return $callback();
        } catch (Exception $e) {
            return response()->json([
                'uploaded' => false,
                'error'    => [
                    'message' => $errorMessage . ': ' . $e->getMessage(),
                ]
            ], 500);
        }
    }
}
