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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['api', 'auth:api']]
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(Language::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });

        $exceptions->render(function (ValidationException $exception) {
            return ApiResponse::customErrorResponse(collect($exception->errors())->flatten()->first(), ['errors' => $exception->errors()], null, Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (AuthorizationException $exception) {
            return ApiResponse::customErrorResponse(__('auth.unauthorized'), [], $exception = null, Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (AuthenticationException $exception) {
            return ApiResponse::customErrorResponse(__('auth.unauthenicated'), [], $exception = null, Response::HTTP_UNAUTHORIZED);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                $previous = $exception->getPrevious();
                if ($previous instanceof ModelNotFoundException) {
                    return ApiResponse::customErrorResponse(__('error.model_not_found'), [], $exception, Response::HTTP_NOT_FOUND);
                }
            }
            return ApiResponse::customErrorResponse(__('error.route_not_found'), [], $exception, Response::HTTP_NOT_FOUND);
        });


        $exceptions->render(function (BadRequestHttpException $exception) {
            return ApiResponse::customErrorResponse($exception->getMessage(), [], $exception, Response::HTTP_BAD_REQUEST);
        });

        $exceptions->render(function (QueryException $exception) {
            return ApiResponse::customErrorResponse($exception->getMessage(), [], $exception);
        });

        $exceptions->render(function (UnauthorizedException $exception) {
            return ApiResponse::customErrorResponse($exception->getMessage(), [], $exception, Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (HttpException $exception) {
            //  dd(get_class($exception));
            return ApiResponse::customErrorResponse($exception->getMessage(), [], $exception);
        });
    })->create();
