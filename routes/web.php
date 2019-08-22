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

// // Test 2
// Route::get('test', function() {

//     // Get User By Info-Field where User's Info is Johan
//     $user = App\User::whereHas('info', function ($query) {
//         $query->where('firstname', '=', 'johan');
//     })->first();

//     dd($user);
// });

// // Test!
// Route::get('test-update-relationed-value', function() {
//     $user = App\User::find(1)->with('info')->first();

//     $user->info->firstname = "Alexander 123";
//     $user->info->save();

//     dd($user->info->firstname);
// });

// Test Middlware
// route::get('middleware', function() {
//     return "jo";
// })->middleware('auth.user.pan');

// // Newsletter
// Route::get('newsletter', function() {
//     dd(Auth()->user()->newsletter);
//     return '';
// });

Route::get('{path}', function () {
    return view('index');
})->where('path', '(.*)');
