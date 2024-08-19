<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PDPAController;
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
Route::get('/admin/pdpa', [PDPAController::class, 'PDPAPage']);
Route::get('/admin/invitations', [InvitationController::class, 'InvitationPage']);
Route::get('/admin/departments', [DepartmentController::class, 'DepartmentPage']);
Route::get('/admin/tenants', [TenantController::class, 'TenantPage']);
Route::get('/admin/operators', [OperatorController::class, 'OperatorPage']);

Route::post('/admin/login', [AuthController::class, 'Login']);
Route::post('/admin/reset', [AuthController::class, 'Reset']);
Route::post('/admin/send_reset_email', [AuthController::class, 'SendResetEmail']);
Route::get('/admin/reset_passwrd', [AuthController::class, 'ResetPasswordPage']);
Route::post('/admin/reset_passwrd', [AuthController::class, 'ResetPassword']);
Route::get('/admin/logout', [AuthController::class, 'Logout']);

Route::post('/admin/get_pdpas', [PDPAController::class, 'GetPDPAs']);
Route::post('/admin/create_pdpa', [PDPAController::class, 'CreatePDPA']);
Route::post('/admin/update_pdpa', [PDPAController::class, 'UpdatePDPA']);
Route::post('/admin/activate_pdpa', [PDPAController::class, 'ActivatePDPA']);
Route::post('/visitors/get_pdpa_consent', [PDPAController::class, 'GetPDPAConsent']);

Route::post('/admin/get_departments', [DepartmentController::class, 'GetDepartments']);
Route::post('/admin/create_department', [DepartmentController::class, 'CreateDepartment']);
Route::post('/admin/update_department', [DepartmentController::class, 'UpdateDepartment']);
Route::post('/admin/update_department2', [DepartmentController::class, 'UpdateDepartment2']);

Route::post('/admin/get_lv1_depts', [TenantController::class, 'GetLv1Depts']);
Route::post('/admin/get_tenants', [TenantController::class, 'GetTenants']);
Route::post('/admin/create_tenant', [TenantController::class, 'CreateTenant']);
Route::post('/admin/send_admin_email', [TenantController::class, 'SendAdminEmail']);
Route::post('/admin/update_tenant', [TenantController::class, 'UpdateTenant']);
Route::post('/admin/update_tenant_edb', [TenantController::class, 'UpdateTenantEDB']);

Route::post('/admin/get_operator_depts', [OperatorController::class, 'GetOperatorDepts']);
Route::post('/admin/get_operators', [OperatorController::class, 'GetOperators']);
Route::post('/admin/create_operator', [OperatorController::class, 'CreateOperator']);
Route::post('/admin/update_operator', [OperatorController::class, 'UpdateOperator']);
Route::post('/admin/update_operator_edb', [OperatorController::class, 'UpdateOperatorEDB']);

Route::post('/admin/get_invitation_depts', [InvitationController::class, 'GetInvitationDepts']);
Route::post('/admin/get_contact', [InvitationController::class, 'GetContactByEmail']);
Route::post('/visitors/get_contact', [InvitationController::class, 'GetVisitorContactByEmail']);
Route::post('/visitors/add_visitor', [InvitationController::class, 'AddVisitor']);
Route::post('/admin/get_invitations', [InvitationController::class, 'GetInvitations']);
Route::post('/admin/get_contacts', [InvitationController::class, 'GetContactsByInviteId']);
Route::post('/admin/upsert_contact', [InvitationController::class, 'UpsertContact']);
Route::post('/admin/create_invitation', [InvitationController::class, 'CreateInvitation']);
Route::post('/admin/send_invite_email', [InvitationController::class, 'SendInviteEmail']);
Route::post('/admin/send_qr_code', [InvitationController::class, 'SendQRCode']);
Route::get('/visitors/invitation/{id}', [InvitationController::class, 'VisitorInvitation']);
Route::post('/visitors/get_invitation', [InvitationController::class, 'GetInvitationById']);
Route::post('/visitors/edit_visitor', [InvitationController::class, 'EditVisitor']);
Route::get('/admin/test', [InvitationController::class, 'TestSendEmail']);
