<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\SuperAdminController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\RoleHasPermissionController;
use App\Http\Controllers\API\UserHasRoleController;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:api', 'verified')->group(function () {
    //SuperAdmin Routes
    Route::post('addCompany', [SuperAdminController::class, 'addCompany']);
    Route::post('activateAdmin', [SuperAdminController::class, 'activateAdmin']);
    // Route::post('setAdmin', [SuperAdminController::class, 'setAdmin']);
    Route::post('deactivateAdmin', [SuperAdminController::class, 'deactivateAdmin']);
    Route::post('removeAdmin', [SuperAdminController::class, 'removeAdmin']);
    //Permission Routes
    Route::post('addPermission', [PermissionController::class, 'addPermission']);
    Route::get('getPermission', [PermissionController::class, 'getPermission']);
    //Role Routes
    Route::post('addRole', [RoleController::class, 'addRole']);
    //RoleHasPermission Routes
    Route::post('addRoleHasPermission', [RoleHasPermissionController::class, 'addRoleHasPermission']);
    Route::get('getRoleHasPermission', [RoleHasPermissionController::class, 'getRoleHasPermission']);
    Route::post('removeRoleHasPermission', [RoleHasPermissionController::class, 'removeRoleHasPermission']);
    //UserHasRole Routes
    Route::post('assignRole', [UserHasRoleController::class, 'assignRole']);
    Route::get('getRole', [UserHasRoleController::class, 'getRole']);
    Route::post('removeRole', [UserHasRoleController::class, 'removeRole']);
    //Admin Routes
    Route::post('addUserToCompany', [AdminController::class, 'addUserToCompany']);
    Route::post('activateUser', [AdminController::class, 'activateUser']);
    //User Routes

    //General Routes
    Route::resource('products', ProductController::class);
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('checkUser', [LoginController::class, 'checkUser']);
});
