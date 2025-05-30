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
Route::prefix('')->group(function () {
    Route::match(array('GET','POST'), '/', function(){ echo 'SITE HOME!'; })->name('site.home');

    Route::group([], function(){
        Route::fallback(function () {
            echo 'SITE 404!';
        })->name('site.404');
    });
});

// ==============================================
// ALL ROUTES MUST HAVE NAME FOR PERMISSION CHECK
// ==============================================
Route::prefix(env('APP_PREFIX_FOLDER'))->group(function () {
    Route::get('/', 'App\Http\Controllers\Login@index')->name('app.login');
    Route::post('/doLogin', 'App\Http\Controllers\Login@doLogin')->name('app.doLogin');
    Route::get('/forgot', 'App\Http\Controllers\Login@forgot')->name('app.forgot');
    Route::post('/doForgot', 'App\Http\Controllers\Login@doForgot')->name('app.doForgot');
    Route::get('/register', 'App\Http\Controllers\Login@register')->name('app.register');
    Route::post('/doRegister', 'App\Http\Controllers\Login@doRegister')->name('app.doRegister');
    Route::get('/googleLogin', 'App\Http\Controllers\Login@googleLogin')->name('app.googleLogin');
    Route::get('/googleLoginCallback', 'App\Http\Controllers\Login@googleLoginCallback')->name('app.googleLoginCallback');
    Route::get('/resetPwd/{idKey}', 'App\Http\Controllers\Login@resetPwd')->name('app.resetPwd')->middleware('signed');
    Route::post('/doResetPwd', 'App\Http\Controllers\Login@doResetPwd')->name('app.doResetPwd')->middleware('signed');
    Route::get('/confirmUser/{key}', 'App\Http\Controllers\Login@confirmUser')->name('app.confirmUser')->middleware('signed');
    Route::get('/avaliation/showMyAvaliation/{codedId}', 'App\Http\Controllers\Avaliation@showMyAvaliation')->name('app.avaliation.showMyAvaliation')->middleware('signed');
    Route::get('/s/{key}', 'App\Http\Controllers\UrlShortController@redirect')->name('app.urlShortController.redirect');

    // ================================================
    // ADD ROUTE PERMISSIONS ON App\Helpers\Permissions
    // ================================================
    Route::middleware(['authWeb'])->group(function () {
        Route::get('/dashboard', 'App\Http\Controllers\Dashboard@index')->name('app.dashboard.index');
        Route::get('/dashboard/clientsWithoutAvaliation30Days', 'App\Http\Controllers\Dashboard@clientsWithoutAvaliation30Days')->name('app.dashboard.clientsWithoutAvaliation30Days');
        Route::get('/dashboard/clientsWithGoalsDueThisWeek', 'App\Http\Controllers\Dashboard@clientsWithGoalsDueThisWeek')->name('app.dashboard.clientsWithGoalsDueThisWeek');
        Route::get('/profile', 'App\Http\Controllers\User@profile')->name('app.user.profile');
        Route::post('/doProfile', 'App\Http\Controllers\User@doProfile')->name('app.user.doProfile');
        Route::get('/changePsw', 'App\Http\Controllers\User@changePsw')->name('app.user.changePsw');
        Route::post('/doChangePsw', 'App\Http\Controllers\User@doChangePsw')->name('app.user.doChangePsw');

        Route::prefix('client')->group(function () {
            Route::get('/', 'App\Http\Controllers\Client@index')->name('app.client.index');
            Route::get('/add', 'App\Http\Controllers\Client@add')->name('app.client.add');
            Route::post('/doSave', 'App\Http\Controllers\Client@doSave')->name('app.client.doSave');
            Route::get('/edit/{codedId}', 'App\Http\Controllers\Client@edit')->name('app.client.edit');
            Route::get('/view/{codedId}', 'App\Http\Controllers\Client@view')->name('app.client.view');
        });

        Route::prefix('goal')->group(function () {
            Route::get('/htmlModalAdd', 'App\Http\Controllers\Goal@htmlModalAdd')->name('app.goal.htmlModalAdd');
            Route::post('/doModalAdd', 'App\Http\Controllers\Goal@doModalAdd')->name('app.goal.doModalAdd');
            Route::post('/doModalRemove', 'App\Http\Controllers\Goal@doModalRemove')->name('app.goal.doModalRemove');
            Route::match(array('GET','POST'), '/htmlModalPastGoals', 'App\Http\Controllers\Goal@htmlModalPastGoals')->name('app.goal.htmlModalPastGoals');
        });

        Route::prefix('avaliation')->group(function () {
            Route::get('/', 'App\Http\Controllers\Avaliation@index')->name('app.avaliation.index');
            Route::get('/htmlModalView', 'App\Http\Controllers\Avaliation@htmlModalView')->name('app.avaliation.htmlModalView');
            Route::get('/htmlModalAdd', 'App\Http\Controllers\Avaliation@htmlModalAdd')->name('app.avaliation.htmlModalAdd');
            Route::post('/doModalAdd', 'App\Http\Controllers\Avaliation@doModalAdd')->name('app.avaliation.doModalAdd');
            Route::get('/htmlModalSelectClient', 'App\Http\Controllers\Avaliation@htmlModalSelectClient')->name('app.avaliation.htmlModalSelectClient');
            Route::get('/htmlModalEdit', 'App\Http\Controllers\Avaliation@htmlModalEdit')->name('app.avaliation.htmlModalEdit');
            Route::get('/photo/{fileName}', 'App\Http\Controllers\Avaliation@showPhoto')->name('app.avaliation.showPhoto');
            Route::get('/viewReport/{codedId}', 'App\Http\Controllers\Avaliation@viewReport')->name('app.avaliation.viewReport');
            Route::get('/viewReportPDF/{codedId}', 'App\Http\Controllers\Avaliation@viewReportPDF')->name('app.avaliation.viewReportPDF');
            Route::get('/htmlModalSendWhats', 'App\Http\Controllers\Avaliation@htmlModalSendWhats')->name('app.avaliation.htmlModalSendWhats');
            Route::post('/doModalSendWhats', 'App\Http\Controllers\Avaliation@doModalSendWhats')->name('app.avaliation.doModalSendWhats');
            Route::get('/htmlModalSendMail', 'App\Http\Controllers\Avaliation@htmlModalSendMail')->name('app.avaliation.htmlModalSendMail');
            Route::post('/doModalSendMail', 'App\Http\Controllers\Avaliation@doModalSendMail')->name('app.avaliation.doModalSendMail');
        });
    });

    Route::group([], function(){
        Route::fallback(function () {
            return view('app.404');
        })->name('app.404');
    });
});
