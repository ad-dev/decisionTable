<?php

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	$file_name = 'test_data';
	if (!file_exists($file_name)) {
		return 'Could not read ' . $file_name;
	}
	Artisan::call('flights:list-claimable', ['csv_filename' => $file_name]);
    return view('index', ['csv_data' => file_get_contents($file_name), 'output' => Artisan::output()]);
});
