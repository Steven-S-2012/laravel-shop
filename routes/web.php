<?php

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

Route::get('/', 'PagesController@root')->name('root');
Route::get('phpinfo', function (){
    phpinfo();
});
Route::group(['middleware'=>'auth'], function(){
    Route::get('/email_verify_notice', 'PagesController@emailVerifyNotice')->name('email_verify_notice');
    Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
    Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');
    //START
    Route::group(['middleware'=>'email_verified'], function(){
        Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
    });

    //END
});



Auth::routes();

/* Already has index 'root' so delete it.
 * Route::get('/home', 'HomeController@index')->name('home');
 */
