<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\OperatorController;

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

Route::get('/admin/login', [AuthController::class, 'LoginPage']);
Route::get('/admin/dashboard', [DashboardController::class, 'DashboardPage']);
Route::get('/admin/invitations', [InvitationController::class, 'InvitationPage']);
Route::get('/admin/departments', [DepartmentController::class, 'DepartmentPage']);
Route::get('/admin/tenants', [TenantController::class, 'TenantPage']);
Route::get('/admin/operators', [OperatorController::class, 'OperatorPage']);

Route::post('/admin/login', [AuthController::class, 'Login']);
Route::get('/admin/logout', [AuthController::class, 'Logout']);

Route::post('/admin/get_sup_depts', [DepartmentController::class, 'GetSupDepts']);
Route::post('/admin/get_depts', [DepartmentController::class, 'GetDepts']);
