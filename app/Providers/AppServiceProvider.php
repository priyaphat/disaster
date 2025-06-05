<?php

namespace App\Providers;

use App\Events\AdminRegistered;
use App\Events\UserRegistered;
use App\Listeners\CreateUserApproval;
use App\Listeners\SendPasswordEmail;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\UserEventSubscriber;
use Carbon\CarbonInterval;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Passport::tokensExpireIn(CarbonInterval::days(15));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));
        date_default_timezone_set('UTC');

        // Event for a user registers to the system
        Event::subscribe(UserEventSubscriber::class);
        JsonResource::withoutWrapping();
    }
}
