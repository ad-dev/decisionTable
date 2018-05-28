<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DecisionTableProvider extends ServiceProvider
{

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('App\Contracts\DecisionTable', 'App\Services\DecisionTable');
	}
}
