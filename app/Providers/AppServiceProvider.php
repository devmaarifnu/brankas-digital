<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Illuminate\Support\Facades\File;

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
        Paginator::useBootstrapFive();
        $models = [
            \App\Models\BHPNU::class,
            \App\Models\Coretax::class,
            \App\Models\OSS::class,
            \App\Models\Informasi::class,
            \App\Models\PDPTK::class,
            \App\Models\Others::class,
            \App\Models\ProfilePengurusCabang::class,
            \App\Models\ProfilePengurusWilayah::class,
            \App\Models\Satpen::class,
            \App\Models\VirtualNPSN::class,
            \App\Models\User::class,
        ];

        foreach ($models as $model) {
            $model::observe(GlobalModelObserver::class);
        }        
    }
}
