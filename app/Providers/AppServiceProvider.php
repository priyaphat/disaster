<?php

namespace App\Providers;

use App\Enums\ProductLogs;
use App\Listeners\UserEventSubscriber;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\AssessmentLogs;
use App\Models\Assessments;
use App\Models\Category;
use App\Models\Config;
use App\Models\DamagedUtility;
use App\Models\Department;
use App\Models\Disaster;
use App\Models\DisasterEarthquake;
use App\Models\DisasterFlood;
use App\Models\DisasterLandslide;
use App\Models\DisasterSmog;
use App\Models\DisasterWildfire;
use App\Models\EmergencyPost;
use App\Models\EmergencyRequest;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\ShelterPopulation;
use App\Models\ShelterStaff;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\News;
use App\Models\Position;
use App\Models\Prefix;
use App\Models\Product;
use App\Models\ProductRequest;
use App\Models\ShelterPoint;
use App\Models\ShelterPosition;
use App\Models\ShelterProduct;
use App\Models\ShelterType;
use App\Models\Unit;
use App\Models\VulnerableGroup;
use App\Observers\ShelterObserver;
use App\Observers\ShelterPopulationObserver;
use App\Observers\ShelterStaffObserver;
use App\Observers\TeamMemberObserver;
use App\Observers\TeamObserver;
use App\Policies\AppointmenLogPolicy;
use App\Policies\AppointmentPolicy;
use App\Policies\AssessmentLogsPolicy;
use App\Policies\AssessmentsPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ConfigPolicy;
use App\Policies\DamageUtilityPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DisasterEarthquakePolicy;
use App\Policies\DisasterFloodPolicy;
use App\Policies\DisasterLandslidePolicy;
use App\Policies\DisasterPolicy;
use App\Policies\DisasterSmogPolicy;
use App\Policies\DisasterWildfirePolicy;
use App\Policies\EmergencyPostPolicy;
use App\Policies\EmergencyRequestPolicy;
use App\Policies\NewsPolicy;
use App\Policies\PopulationPolicy;
use App\Policies\PositionPolicy;
use App\Policies\PrefixPolicy;
use App\Policies\ProductLogsPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductRequestPolicy;
use App\Policies\ShelterPointPolicy;
use App\Policies\ShelterPolicy;
use App\Policies\ShelterPopulationPolicy;
use App\Policies\ShelterPositionPolicy;
use App\Policies\ShelterProductPolicy;
use App\Policies\ShelterStaffPolicy;
use App\Policies\ShelterTypePolicy;
use App\Policies\TeamMemberPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UnitPolicy;
use App\Policies\UserPolicy;
use App\Policies\VulnerableGroupPolicy;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

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
        Passport::tokensExpireIn(CarbonInterval::days(1));
        Passport::refreshTokensExpireIn(CarbonInterval::days(7));
        Passport::personalAccessTokensExpireIn(CarbonInterval::days(1));
        // Passport::tokensExpireIn(CarbonInterval::minutes(15));
        // Passport::refreshTokensExpireIn(CarbonInterval::minutes(15));
        // Passport::personalAccessTokensExpireIn(CarbonInterval::minutes(15));

        date_default_timezone_set('Asia/Bangkok');

        // Set Rate Limit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(240)->by($request->user()?->id ?: $request->ip());
        });

        // Event for a user registers to the system
        Event::subscribe(UserEventSubscriber::class);
        JsonResource::withoutWrapping();

        ShelterPopulation::observe(ShelterPopulationObserver::class);
        TeamMember::observe(TeamMemberObserver::class);
        ShelterStaff::observe(ShelterStaffObserver::class);
        Shelter::observe(ShelterObserver::class);
        Team::observe(TeamObserver::class);

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(News::class, NewsPolicy::class);

        Gate::policy(DamagedUtility::class, DamageUtilityPolicy::class);
        Gate::policy(Disaster::class, DisasterPolicy::class);
        Gate::policy(DisasterFlood::class, DisasterFloodPolicy::class);
        Gate::policy(DisasterSmog::class, DisasterSmogPolicy::class);
        Gate::policy(DisasterWildfire::class, DisasterWildfirePolicy::class);
        Gate::policy(DisasterEarthquake::class, DisasterEarthquakePolicy::class);
        Gate::policy(DisasterLandslide::class, DisasterLandslidePolicy::class);

        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(AppointmentLog::class, AppointmenLogPolicy::class);

        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(TeamMember::class, TeamMemberPolicy::class);

        Gate::policy(Shelter::class, ShelterPolicy::class);
        Gate::policy(ShelterStaff::class, ShelterStaffPolicy::class);
        Gate::policy(ShelterPosition::class, ShelterPositionPolicy::class);
        Gate::policy(ShelterPoint::class, ShelterPointPolicy::class);

        Gate::policy(Population::class, ShelterPopulationPolicy::class);
        Gate::policy(Population::class, PopulationPolicy::class);

        Gate::policy(Config::class, ConfigPolicy::class);

        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Unit::class, UnitPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Prefix::class, PrefixPolicy::class);
        Gate::policy(Position::class, PositionPolicy::class);

        Gate::policy(ShelterProduct::class, ShelterProductPolicy::class);
        Gate::policy(ProductRequest::class, ProductRequestPolicy::class);
        Gate::policy(ProductLogs::class, ProductLogsPolicy::class);

        Gate::policy(EmergencyPost::class, EmergencyPostPolicy::class);
        Gate::policy(EmergencyRequest::class, EmergencyRequestPolicy::class);

        Gate::policy(Assessments::class, AssessmentsPolicy::class);
        Gate::policy(AssessmentLogs::class, AssessmentLogsPolicy::class);

        Gate::policy(VulnerableGroup::class, VulnerableGroupPolicy::class);

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

        if (env('DEV_MAIL')) {
            Mail::alwaysTo(env('DEV_MAIL_TO', 'gensoft.dev.tech@gmail.com'));
        }
    }
}
