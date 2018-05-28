<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CountriesFeedProvider extends ServiceProvider
{

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('CountriesFeed', 'App\Services\Countries');
	}
}
