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
Route::get("$_VERSION/login", 'Platform\Login@getLogin'); // buat cek kalo ada masalah login
Route::post("$_VERSION/logout", 'Platform\Login@logout');

Route::middleware(['auth.header','auth.access'])->group(function () {
    
    $_VERSION       = "1.0";
    $_SRVC_PLATFORM = "admin";
    $_SRVC_HOTEL    = "hotel";
    $_SRVC_FNB      = "fnb";
    $_INGREDIENTS   = "ingredients";
    $_CASH_OPNAME   = "cash-opname";
    $_CASHIER_ANNOTATION = "cashier-annotation";


    // Admin - Page Content
    Route::post("$_VERSION/$_SRVC_PLATFORM/dashboard", 'Platform\AdminContent@dashboardContent');
    Route::post("$_VERSION/$_SRVC_PLATFORM/logs", 'Platform\Log@getLogs');

    // Admin - Request
    Route::post("$_VERSION/$_SRVC_PLATFORM/user/get", 'Platform\User@getUserData');

    // Fnb In Here
    Route::post("$_VERSION/$_SRVC_FNB/dashboard/menu", 'Fnb\Dashboard\FnbContentController@FnbMenuContent');
    Route::post("$_VERSION/$_SRVC_FNB/$_INGREDIENTS/list", 'Fnb\IngredientsController@getListIngredients');
    Route::post("$_VERSION/$_SRVC_FNB/$_INGREDIENTS/detail", 'Fnb\IngredientsController@getIngredientDetail');
    Route::post("$_VERSION/$_SRVC_FNB/$_INGREDIENTS/detail", 'Fnb\IngredientsController@getIngredientDetail');
    

    // Route::post("$_VERSION/$_SRVC_FNB/$_CASH_OPNAME/list", 'Fnb\CashOpnameController@getListIngredients');
    // Route::post("$_VERSION/$_SRVC_FNB/$_CASHIER_ANNOTATION/list", 'Fnb\CashierAnnotationController@getListIngredients');

});

Route::get("$_VERSION/test", function() {
    return "Ini Budi";
});
