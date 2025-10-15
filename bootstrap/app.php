<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\HandleSessionTimeout;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global web middleware
        $middleware->web(append: [
            HandleSessionTimeout::class,
        ]);

        // Middleware aliases
        $middleware->alias([
            'admin' => IsAdmin::class,
            'permission' => CheckPermission::class,
        ]);

        // Priority middleware (runs first)
        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            HandleSessionTimeout::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            IsAdmin::class,
            CheckPermission::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Sync HMIS data every 2 minutes
        $schedule->command('hmis:sync')
                 ->everyTwoMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Clean up expired sessions daily
        $schedule->command('session:gc')
                 ->daily()
                 ->at('03:00');

        // Optional: Generate daily reports
        $schedule->command('reports:generate-daily')
                 ->dailyAt('00:05')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->onFailure(function () {
                     \Log::error('Daily report generation failed');
                 });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 403 Forbidden (Permission Denied)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [
                    'message' => $e->getMessage() ?: 'Unauthorized access. You do not have permission to view this page.'
                ], 403);
            }
        });

        // Handle session timeout for JSON requests
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Session expired',
                    'message' => 'Your session has expired. Please login again.',
                    'redirect' => route('login')
                ], 401);
            }
        });
    })->create();
