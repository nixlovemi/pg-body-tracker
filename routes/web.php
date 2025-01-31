<?php

use Illuminate\Support\Facades\Route;

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

// ==============================================
// ALL ROUTES MUST HAVE NAME FOR PERMISSION CHECK
// ==============================================
Route::group([], function(){
    Route::fallback(function () {
        // TODO: make a 404 page
        echo '404'; die;
        return view('404');
    })->name('site.404');
});

Route::prefix('app')->group(function () {
    Route::get('/', 'App\Http\Controllers\Login@index')->name('app.login');
    Route::post('/doLogin', 'App\Http\Controllers\Login@doLogin')->name('app.doLogin');

    // ================================================
    // ADD ROUTE PERMISSIONS ON App\Helpers\Permissions
    // ================================================
    Route::middleware(['authWeb'])->group(function () {
        Route::get('/dashboard', 'App\Http\Controllers\Dashboard@index')->name('app.dashboard.index');

        Route::prefix('client')->group(function () {
            Route::get('/', 'App\Http\Controllers\Client@index')->name('app.client.index');
            Route::get('/add', 'App\Http\Controllers\Client@add')->name('app.client.add');
            Route::post('/doSave', 'App\Http\Controllers\Client@doSave')->name('app.client.doSave');
            Route::get('/edit/{codedId}', 'App\Http\Controllers\Client@edit')->name('app.client.edit');
        });

        Route::prefix('goal')->group(function () {
            Route::get('/htmlModalAdd', 'App\Http\Controllers\Goal@htmlModalAdd')->name('app.goal.htmlModalAdd');
            Route::post('/doModalAdd', 'App\Http\Controllers\Goal@doModalAdd')->name('app.goal.doModalAdd');
            Route::post('/doModalRemove', 'App\Http\Controllers\Goal@doModalRemove')->name('app.goal.doModalRemove');
        });
    });
});
