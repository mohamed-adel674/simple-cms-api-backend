<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // إذا لم تقم بتعريف Rate Limiter في AppServiceProvider، يمكنك وضعه هنا
        // RateLimiter::for('api', function (Request $request) {
        //     return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        // });

        $this->routes(function () {
            
            // تحميل مسارات API (هذا هو الجزء الذي كنت تحتاجه)
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // تحميل مسارات الويب
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}