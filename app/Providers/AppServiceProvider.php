<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use Livewire\Livewire;
use App\Http\Livewire\Demande\AgrementComponent;
use App\Http\Livewire\Demande\Step\TypeStepComponent;

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
        Schema::defaultStringLength(255);
        Paginator::useBootstrap();

        Livewire::component('demandes-agrement', AgrementComponent::class);
        Livewire::component('type-demande-step', TypeStepComponent::class);

        //\URL::forceScheme('https');

    }
}
