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

        
        // Company
        Route::get('companies/all', 'User\UserController@getCompanies');
        Route::patch('user/company/set', 'User\UserController@setCompanyId');
        Route::post('company/create', 'User\UserController@createCompany');
        Route::patch('company/update', 'User\UserController@updateCompany');
        
        // Department
        Route::get('departments/all', 'User\UserController@getDepartments');
        Route::patch('user/department/set', 'User\UserController@setDepartmentId');
        Route::post('department/create', 'User\UserController@createDepartment');
        Route::patch('department/update', 'User\UserController@updateDepartment');

        // Locations
        Route::get('locations/all', 'User\UserController@getLocations');
        Route::patch('user/location/set', 'User\UserController@setLocationId');
        Route::post('location/create', 'User\UserController@createLocation');
        Route::patch('location/update', 'User\UserController@updateLocation');

        // Groups
        Route::get('groups-moderating', 'Backend\GroupController@getGroupsModerating'); // Moderating Groups
        Route::get('get-group', 'Backend\GroupController@getGroup'); // Get Group Content
        Route::post('group/create', 'Backend\GroupController@createGroup');
        Route::patch('group/update', 'Backend\GroupController@updateGroup');

        // Get Users
        Route::get('users-created', 'Backend\BackendController@getCreatedUsers'); // Created Users

        // Get a Generated PAN
        Route::get('get-random-pan', 'Backend\BackendController@getRandomPan');

        // Update Users Groups
        Route::post('add-user-to-group', 'Backend\GroupController@addUserToGroup');
        Route::post('remove-user-from-group', 'Backend\GroupController@removeUserFromGroup');

        // Change User(s)
        Route::patch('update-created-users', 'Backend\BackendController@updateCreatedUsers');
        Route::patch('delete-created-user', 'Backend\BackendController@deleteUser');
        Route::post('create-users', 'Backend\BackendController@createUsers')->middleware('auth.user.can_create_users');

        // User who can Create Surveys
        Route::group(['middleware' => ['auth.user.can_create_surveys'], 'prefix' => 'backend' ], function()
        {
            // Get Survey(s)
            Route::get('surveys-created', 'Backend\SurveyController@getCreatedSurveys');
            Route::get('survey-created',  'Backend\SurveyController@getCreatedSurvey');

            // Change Surveys
            Route::patch('update-created-survey', 'Backend\SurveyController@updateCreatedSurvey');
            Route::patch('delete-created-survey', 'Backend\SurveyController@deleteSurvey');
            Route::post('create-survey', 'Backend\SurveyController@createUsers');
        });
    });
});
