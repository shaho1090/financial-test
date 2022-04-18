<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CurrencyNotFoundException extends Exception
{
    public function render($request): JsonResponse
    {
        return  response()->json([
            'error' => true,
            'message' => 'The currency was not found!',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
