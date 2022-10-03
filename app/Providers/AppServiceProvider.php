<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
		if(env('APP_ENV') == 'production') {
			$url->formatScheme('https');
		}
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		if(env('APP_ENV') == 'production') {
			$this->app['request']->server->set('HTTPS', true);
		}
    }
}
