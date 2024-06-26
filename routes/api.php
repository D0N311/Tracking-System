<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SuperAdminController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\CodeCheckController;
use App\Http\Controllers\API\ItemsController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\RoleHasPermissionController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserHasRoleController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\VerifyEmailController;
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

Route::post('register', [AuthController::class, 'register'])->middleware('throttle:3,1');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:3,1');
Route::post('forgotPassword', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
Route::post('resetPassword', [AuthController::class, 'resetPassword'])->middleware('throttle:3,1');

Route::post('password/email',  ForgotPasswordController::class)->middleware('throttle:3,1');
Route::post('password/code/check', CodeCheckController::class)->middleware('throttle:3,1');
Route::post('password/reset', ResetPasswordController::class)->middleware('throttle:3,1');

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
        Route::get('transactionIndex', [TransactionController::class, 'transactionIdex']);
        Route::post('approveTransaction', [TransactionController::class, 'approveTransaction']);
        Route::get('inProgressIndex', [TransactionController::class, 'inProgressIndex']);
        Route::post('cancelTransaction', [TransactionController::class, 'cancelTransaction']);
        Route::get('cancelIndex', [TransactionController::class, 'cancelIndex']);
        Route::get('historyIndex', [TransactionController::class, 'HistoryIndex']);
        Route::get('deliverIndex', [TransactionController::class, 'deliverIndex']);
    });

    // User Routes
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::post('addItems', [ItemsController::class, 'addItems']);
        Route::get('toRecieveIndex', [TransactionController::class, 'userToRecieveIndex']);
        Route::post('recieveTransaction', [TransactionController::class, 'recieveTransaction']);
        Route::get('receivedIndex', [TransactionController::class, 'transRecievedIndex']);
    });

    // General Routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('checkUser', [AuthController::class, 'checkUser']);
    Route::get('searchInput', [VerifyEmailController::class, 'searchInput']);
    Route::get('noRoleIndex', [SuperAdminController::class, 'noRoleIndex']);
    Route::post('storeItem', [ItemsController::class, 'store']);
    Route::get('companyItems', [ItemsController::class, 'companyItems']);
    Route::get('userItems', [ItemsController::class, 'userItems']);
    Route::post('addTransaction', [TransactionController::class, 'AddTransaction']);
    Route::post('resetPassword', [AuthController::class, 'resetPassword']);

    Route::get('test', function() {
        return response('Testing');
    });
});
