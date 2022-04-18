<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CurrencyExchangeURLException extends Exception
{
    public function render($request): JsonResponse
    {
        return  response()->json([
            'error' => true,
            'message' => 'The currency exchange URL was not found or not set!',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
