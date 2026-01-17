<?php

namespace App\Providers;

use Livewire\Livewire;
use App\Models\Setting;
use App\Models\CourseClass;
use App\Http\Livewire\DeleteUser;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\Observers\CourseClassObserver;
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
        CourseClass::observe(CourseClassObserver::class);
        View::composer('*', function ($view) {
            $view->with('siteSettings', Setting::first());
        });

        Blade::component('layouts.mentor', 'mentor-layout');

        // Alias kedua (untuk file yang sama)
        Blade::component('layouts.managecourse', 'managecourse-layout');
    }
}
