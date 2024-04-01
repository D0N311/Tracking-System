<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\SuperAdminController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ItemsController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\RoleHasPermissionController;
use App\Http\Controllers\API\UserHasRoleController;
use App\Http\Controllers\VerifyEmailController;
use App\Models\Items;
use App\Models\Role;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PharIo\Manifest\Email;

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
    // SuperAdmin Routes
    Route::group(['prefix' => 'superadmin', 'as' => 'superadmin.'], function () {
        Route::post('addCompany', [SuperAdminController::class, 'addCompany']);
        Route::get('companyIndex', [SuperAdminController::class, 'companyIndex']);
        Route::post('activateAdmin', [SuperAdminController::class, 'activateAdmin']);
        Route::get('adminIndex', [SuperAdminController::class, 'adminIndex']);
        Route::post('deactivateAdmin', [SuperAdminController::class, 'deactivateAdmin']);
        Route::post('removeAdmin', [SuperAdminController::class, 'removeAdmin']);
        Route::post('setAdmin', [SuperAdminController::class, 'setAdmin']);
    });

    // Permission Routes
    Route::group(['prefix' => 'permission', 'as' => 'permission.'], function () {
        Route::post('addPermission', [PermissionController::class, 'addPermission']);
        Route::get('getPermission', [PermissionController::class, 'getPermission']);
    });

    // Role Routes
    Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
        Route::post('addRole', [RoleController::class, 'addRole']);
    });

    // RoleHasPermission Routes
    Route::group(['prefix' => 'roleHasPermission', 'as' => 'roleHasPermission.'], function () {
        Route::post('addRoleHasPermission', [RoleHasPermissionController::class, 'addRoleHasPermission']);
        Route::get('getRoleHasPermission', [RoleHasPermissionController::class, 'getRoleHasPermission']);
        Route::post('removeRoleHasPermission', [RoleHasPermissionController::class, 'removeRoleHasPermission']);
    });

    // UserHasRole Routes
    Route::group(['prefix' => 'userHasRole', 'as' => 'userHasRole.'], function () {
        Route::post('assignRole', [UserHasRoleController::class, 'assignRole']);
        Route::get('getRole', [UserHasRoleController::class, 'getRole']);
        Route::post('removeRole', [UserHasRoleController::class, 'removeRole']);
    });

    // Admin Routes
    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::post('addUserToCompany', [AdminController::class, 'addUserToCompany']);
        Route::post('activateUser', [AdminController::class, 'activateUser']);
        Route::get('userIndex', [AdminController::class, 'userIndex']);
        Route::post('deactivateUser', [AdminController::class, 'deactivateUser']);
    });

    // User Routes
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::post('addItems', [ItemsController::class, 'addItems']);
    });

    // General Routes
    Route::resource('products', ProductController::class);
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('checkUser', [LoginController::class, 'checkUser']);
    Route::get('searchInput', [VerifyEmailController::class, 'searchInput']);
    Route::get('noRoleIndex', [SuperAdminController::class, 'noRoleIndex']);
    Route::post('storeItem', [ItemsController::class, 'store']);
});
