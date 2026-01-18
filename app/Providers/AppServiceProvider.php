<?php

namespace App\Providers;

use App\Http\Livewire\DeleteUser;
use App\Models\CourseClass;
use App\Models\Setting;
use App\Observers\CourseClassObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
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
        CourseClass::observe(CourseClassObserver::class);
        View::composer('*', function ($view) {
            $view->with('siteSettings', Setting::first());
        });

        Blade::component('layouts.mentor', 'mentor-layout');

        Blade::component('layouts.admin', 'admin-layout');

        // Alias kedua (untuk file yang sama)
        Blade::component('layouts.managecourse', 'managecourse-layout');
    }
}
