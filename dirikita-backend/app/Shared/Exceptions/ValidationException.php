<?php

namespace App\Shared\Exceptions;

use Illuminate\Validation\ValidationException as LaravelValidationException;

class ValidationException extends LaravelValidationException
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $this->errors(),
                ],
            ], 422);
        }

        return parent::render($request);
    }
}

