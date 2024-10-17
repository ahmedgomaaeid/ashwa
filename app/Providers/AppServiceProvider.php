<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Livewire::component(
            'filament.resources.product-resource.relation-managers.images-relation-manager',
            \App\Filament\Resources\ProductResource\RelationManagers\ImagesRelationManager::class
        );
    }
}
