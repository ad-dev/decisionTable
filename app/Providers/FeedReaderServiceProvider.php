<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FeedReaderServiceProvider extends ServiceProvider
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
		$this->app->bind('App\Contracts\FeedReaderInterface', 'App\Services\CurlReader');
	}
}
