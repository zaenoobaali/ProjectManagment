<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Standard shape for a successful API response: { message, data }.
     */
    protected function successResponse(mixed $data = null, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Standard shape for a failed API response: { message, errors?, data? }.
     */
    protected function errorResponse(string $message, int $code = 400, mixed $errors = null, mixed $data = null): JsonResponse
    {
        $payload = ['message' => $message];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $code);
    }
}
