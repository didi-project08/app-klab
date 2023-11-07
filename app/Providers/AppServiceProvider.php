<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        If (env('APP_ENV') !== 'local') {
            \URL::forceScheme('https');
        }
        
        $project_title = env('SYS_NAME');
        View::share('title', $project_title);

        config(['app.locale' => 'id']);
	    Carbon::setLocale('id');
    }
}