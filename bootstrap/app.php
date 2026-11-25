<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\RiderMiddleware;
use App\Http\Middleware\SellerMiddleware;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request): string {
            if ($request->routeIs('verification.verify')) {
                $role = User::query()->whereKey($request->route('id'))->value('role');

                if ($role === 'seller') {
                    return route('seller.login');
                }
            }

            return route('login');
        });

        $middleware->alias([
            'AdminMiddleware' => AdminMiddleware::class,
            'RiderMiddleware' => RiderMiddleware::class,
            'SellerMiddleware' => SellerMiddleware::class,
            'CustomerMiddleware' => CustomerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // $exceptions->render(function (NotFoundHttpException $e, $request) {

        //     if ($request->is('admin/*') || $request->is('seller/*')) {
        //         return response()->view('errors.dashboard-404', [], 404);
        //     }

        //     return response()->view('errors.404', [], 404);
        // });
    })->create();
