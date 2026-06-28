<?php

use App\Modules\Balances\Application\Exceptions\InsufficientBalanceException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (AuthenticationException $e, $request) {
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

        $exceptions->render(function (AuthorizationException | AccessDeniedHttpException $e, $request) {
            Log::warning('authorization.denied', [
                'user_id'        => $request->user()?->id,
                'method'         => $request->method(),
                'path'           => $request->path(),
                'correlation_id' => $request->header('X-Correlation-ID'),
            ]);

            return response()->json([
                'message' => 'Action is forbidden.',
            ], Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (InsufficientBalanceException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($e instanceof ValidationException) {
                return null;
            }

            if ($request->expectsJson()) {
                Log::error('Unhandled exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'message' => 'Internal Server Error',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();
