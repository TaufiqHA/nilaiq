<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureRecordOwnership;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\MapelMiddleware;
use App\Http\Middleware\WaliKelasMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'mapel' => MapelMiddleware::class,
            'wali_kelas' => WaliKelasMiddleware::class,
            'admin' => AdminMiddleware::class,
            'subscribed' => EnsureSubscriptionActive::class,
            'owns.records' => EnsureRecordOwnership::class,
        ]);

        $middleware->preventRequestForgery(except: [
            'midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
