<?php

namespace App\Providers;

use App\Events\AdminRegistered;
use App\Events\UserRegistered;
use App\Listeners\CreateUserApproval;
use App\Listeners\SendPasswordEmail;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\UserEventSubscriber;
use App\Models\ShelterStaff;
use App\Models\TeamMember;
use App\Observers\ShelterStaffObserver;
use App\Observers\TeamMemberObserver;
use Carbon\CarbonInterval;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;
use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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
        date_default_timezone_set('Asia/Bangkok');
        // Event for a user registers to the system
        Event::subscribe(UserEventSubscriber::class);
        JsonResource::withoutWrapping();

        TeamMember::observe(TeamMemberObserver::class);
        ShelterStaff::observe(ShelterStaffObserver::class);
        // API Document
        Scramble::configure()
            ->expose(
                ui: '/api/docs',
                document: '/docs/v1/openapi.json',
            );

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
        
        Mail::alwaysTo(env('DEV_MAIL_TO', 'maikung1720@gmail.com'));
    }
}
