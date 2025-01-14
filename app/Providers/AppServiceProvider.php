<?php

namespace App\Providers;

use App\Models\students;
use App\Observers\StudentObserver;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Milon Barcode provider
    //    $this->app->register(Maatwebsite\Excel\ExcelServiceProvider::class);

    //    // Register the alias dynamically
    //    $loader = AliasLoader::getInstance();
    //    $loader->alias('Excel', Maatwebsite\Excel\Facades\Excel::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        students::observe(StudentObserver::class);
    }
}
