<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Client::class => \App\Policies\ClientPolicy::class,
        \App\Models\Avaliation::class => \App\Policies\AvaliationPolicy::class,
        \App\Models\Goal::class => \App\Policies\GoalPolicy::class,
        \App\Models\CheckinConfig::class => \App\Policies\CheckinConfigPolicy::class,
        \App\Models\UserPlans::class => \App\Policies\UserPlansPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
