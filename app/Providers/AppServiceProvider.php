<?php

namespace App\Providers;

use App\Models\Candidature;
use App\Models\DocumentCandidature;
use App\Policies\CandidaturePolicy;
use App\Policies\DocumentCandidaturePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Candidature::class, CandidaturePolicy::class);
        Gate::policy(DocumentCandidature::class, DocumentCandidaturePolicy::class);
    }
}
