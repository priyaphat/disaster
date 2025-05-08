<?php

use App\Http\Middleware\Language;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Traits\ApiResponserTrait;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->append(Language::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function (ValidationException $exception) {
            return ApiResponse::customErrorResponse($exception->getMessage(), ['errors' => $exception->errors()], null, Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (AuthorizationException $exception) {
            return ApiResponse::customErrorResponse(__('auth.unauthorized'), [], $exception = null, Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (AuthenticationException $exception) {
            return ApiResponse::customErrorResponse(__('auth.unauthenicated'), [], $exception = null, Response::HTTP_UNAUTHORIZED);
        });

        $exceptions->render(function (QueryException $exception) {
            return ApiResponse::customErrorResponse($exception->getMessage(), [], $exception);
        });

        $exceptions->render(function (HttpException $exception) {
            return ApiResponse::customErrorResponse($exception->getMessage(), [], $exception);
        });
    })->create();
