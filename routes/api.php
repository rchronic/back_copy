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

$_VERSION       = "1.0";

Route::post("$_VERSION/login", 'Platform\Login@login');
// Route::get("$_VERSION/login", 'Platform\Login@getLogin');
Route::post("$_VERSION/logout", 'Platform\Login@logout');

Route::middleware(['auth.header','auth.access'])->group(function () {
    
    $_VERSION       = "1.0";
    $_SRVC_PLATFORM = "admin";

    // Admin - Page Content
    Route::post("$_VERSION/$_SRVC_PLATFORM/dashboard", 'Platform\AdminContent@dashboardContent');
    Route::post("$_VERSION/$_SRVC_PLATFORM/logs", 'Platform\Log@getLogs');

    // Admin - Request
    Route::post("$_VERSION/$_SRVC_PLATFORM/user/get", 'Platform\User@getUserData');

});


Route::get("$_VERSION/test", function() {
    return "Ini Budi";
});
