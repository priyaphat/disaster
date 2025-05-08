<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiResponse implements Responsable
{
    public function __construct(
        private bool $success = true,
        private mixed $message = null,
        private mixed $data,
        private array $customResponse= [],
        private ?Throwable $exception = null,
        private int $httpCode = Response::HTTP_CREATED,
    ) {}

    public function toResponse($request)
    {
        $response = [
            'success' => $this->success,
            'code' => $this->httpCode,
            'message' => $this->message
        ];

        if (app()->environment('local', 'development') && $this->exception) {
            $response['stack_trace'] = $this->exception->getTrace();
        }

        if (!is_null($this->data)) {
            $response['data'] =  $this->data;
        }

        if (!empty($this->customResponse)) {
            $response += $this->customResponse;
        }

        return response()->json(
            data: $response,
            status: $this->httpCode,
            options: JSON_UNESCAPED_UNICODE
        );
    }

    public static function customSuccessResponse($message = "Custom Response", array $customResponse = [], $httpCode = Response::HTTP_OK)
    {
        return new static(true, $message, $data = null, $customResponse, $exception = null, $httpCode);
    }

    public static function successResponse($message = "OK", $data = null, array $customResponse = [])
    {
        return new static(true, $message, $data, $customResponse, $exception = null, Response::HTTP_OK);
    }

    public static function createdResponse($message = "OK", $data = null)
    {
        return new static(true, $message, $data, $customResponse = [], $exception = null, Response::HTTP_CREATED);
    }

    public static function customErrorResponse($message = "Custom Error Response", array $customResponse = [], ?Throwable $exception = null, $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return new static(false, $message, $data = null, $customResponse, $exception, $httpCode);
    }

    public static function errorResponse($message = "Error", $httpCode = Response::HTTP_BAD_REQUEST)
    {
        return new static(false, $message, $data = null, $customResponse = [], $exception = null, $httpCode);
    }
}
