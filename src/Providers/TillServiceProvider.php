<?php

namespace ArtisanBuild\Till\Providers;

use ArtisanBuild\Till\Livewire\PricingSectionComponent;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Override;

class TillServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/till.php', 'till');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'till');
    }

    public function boot(): void
    {

        $this->publishes([
            __DIR__.'/../../config/till.php' => config_path('till.php'),
        ], 'till');

        Livewire::component('till:pricing-section', PricingSectionComponent::class);
    }
}
