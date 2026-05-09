<?php

namespace App\Shared\Traits;

use Illuminate\Http\JsonResponse;

/**
 * ApiResponseTrait — Respuestas JSON estandarizadas
 *
 * Centraliza el formato de todas las respuestas de la API para garantizar
 * consistencia en la estructura JSON que recibe el frontend (Next.js).
 *
 * Estructura exitosa:
 * {
 *   "success": true,
 *   "message": "...",
 *   "data": { ... }
 * }
 *
 * Estructura de error:
 * {
 *   "success": false,
 *   "message": "...",
 *   "errors": { ... }
 * }
 */
trait ApiResponseTrait
{
    /**
     * Respuesta exitosa genérica (HTTP 200 por defecto).
     */
    protected function successResponse(mixed $data, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Respuesta de error con mensaje descriptivo.
     * El parámetro $errors permite incluir errores de validación detallados.
     */
    protected function errorResponse(string $message, int $code, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta para recursos creados exitosamente (HTTP 201).
     */
    protected function createdResponse(mixed $data, string $message = 'Recurso creado exitosamente.'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }
}
