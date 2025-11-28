<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

/**
 * Class ApiResponse
 *
 * Utility class for creating consistent API responses across the application.
 * Provides standardized success and error response formats.
 *
 * @package App\Http\Resources
 */
class ApiResponse
{
    /**
     * Create a successful API response.
     *
     * @param mixed|null $data Response data
     * @param string $message Success message
     * @param int $code HTTP status code
     * @return JsonResponse JSON response
     */
    public static function success(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Create an error API response.
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param mixed|null $errors Validation errors or additional error details
     * @return JsonResponse JSON response
     */
    public static function error(string $message = 'Error', int $code = 400, mixed $errors = null): JsonResponse
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
     * Create a validation error response.
     *
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return JsonResponse JSON response
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, 422, $errors);
    }

    /**
     * Create a not found response.
     *
     * @param string $message Not found message
     * @return JsonResponse JSON response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404);
    }

    /**
     * Create an unauthorized response.
     *
     * @param string $message Unauthorized message
     * @return JsonResponse JSON response
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, 401);
    }

    /**
     * Create a forbidden response.
     *
     * @param string $message Forbidden message
     * @return JsonResponse JSON response
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, 403);
    }
}
