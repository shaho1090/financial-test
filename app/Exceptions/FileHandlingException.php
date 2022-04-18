<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FileHandlingException extends Exception
{
    public function render($request): JsonResponse
    {
        return  response()->json([
            'error' => true,
            'message' => 'There is a problem with reading file',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
