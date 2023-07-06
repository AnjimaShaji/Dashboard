<?php
use App\CustomLibraries\Calllog;
use MongoDB\Client as Mongo;
use Illuminate\Support\Facades\DB;
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
    return view('auth.login');
});
// Auth::routes();
Route::get('change-password','Password\ChangePasswordController@changePassword');
Route::post('update-password','Password\ChangePasswordController@updatePassword');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

//Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth', 'acl'])->group(function () {

    Route::prefix('admin')->group(function () {
        Route::prefix('store')->group(function () {
            Route::get('/', 'Admin\StoreController@details');
            Route::get('edit/{storeId?}', 'Admin\StoreController@edit');
            Route::post('update', 'Admin\StoreController@update');
            Route::get('workingHours/{id}', 'Admin\StoreController@editWorkingHours');
            Route::get('get-all-filter-params', 'Admin\StoreController@getAllFilterParams');
        });
    	
    	Route::prefix('reports')->group(function () {
	    	Route::get('/', 'Admin\ReportsController@details');
	    	Route::get('chart', 'Admin\ReportsController@callStatusByDay');
	    	Route::get('call-duration-average', 'Admin\ReportsController@callDurationAverage');
	    	Route::get('export', 'Admin\ReportsController@export');
	    	Route::get('call/{id}', 'Admin\ReportsController@callDetails');
            Route::get('get-dom-rsm/{domId}', 'Admin\ReportsController@getDomRsmJson');
            Route::get('get-rsm-dealer/{rsmId}', 'Admin\ReportsController@getRsmDealerJson');
            Route::get('get-rsm/', 'Admin\ReportsController@getRsmJson');
            Route::get('get-dealer/', 'Admin\ReportsController@getDealerJson');
            Route::get('is-background-export', 'Admin\ReportsController@getBackgroundExportJson');
            Route::get('process-export', 'Admin\ReportsController@processExportJson');
            Route::get('get-csv-file', 'Admin\ReportsController@getCsvFileJson');
            Route::get('get-filter-params','Admin\ReportsController@getFilterParamsJson');
            Route::get('get-filter-stores','Admin\ReportsController@getFilterStores');
            Route::get('get-stores-by-city/{id}', 'Admin\ReportsController@getStoresByCity');
            Route::get('get-stores-by-state/{id}', 'Admin\ReportsController@getStoresByState');
            Route::get('get-cities-by-state/{id}', 'Admin\ReportsController@getCitiesByState');
            Route::get('get-location-by-state/{id}', 'Admin\ReportsController@getLocationByState');
            Route::get('get-location-by-city/{id}', 'Admin\ReportsController@getLocationByCity');
            Route::get('department-call-count', 'Admin\ReportsController@departmentViseCallCount');
            Route::get('status-vise-count', 'Admin\ReportsController@callStatusViseCount');
            Route::get('dealer-missed-count', 'Admin\ReportsController@dealerMissedCount');
            Route::get('unique-and-repeated-count', 'Admin\ReportsController@uniqueAndRepeatedCount');
            Route::get('store-missed-count/', 'Admin\ReportsController@storeMissedCount');
            Route::get('month-wise-status', 'Admin\ReportsController@monthWiseStatus');
        });
        Route::get('dashboard', 'Admin\ReportsController@dashboard');
        Route::prefix('zero-call-stores')->group(function () {
            Route::get('/', 'Admin\ZeroCallStoresController@index');
            Route::get('/export', 'Admin\ZeroCallStoresController@export');
        });
	});
    
});
