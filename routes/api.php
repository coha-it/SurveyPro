<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('loginpan', 'Auth\LoginController@attemptLoginPan');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('email/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', 'User\UserController@self');

    Route::group(['middleware' => ['auth.user.email' ] ], function() {
        Route::patch('settings/profile', 'Settings\ProfileController@update');
        Route::patch('settings/password', 'Settings\PasswordController@update');

        Route::patch('/user/company/set', 'User\Usercontroller@setCompanyId');
        Route::get('/companies/all', 'User\Usercontroller@getAllCompanies');
        Route::post('/company/create', 'User\Usercontroller@createCompany');
        Route::patch('/company/update', 'User\Usercontroller@updateCompany');
    });
});
