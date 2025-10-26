<?php

use App\Core\Response;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\OutgoingLetterController;
use App\Http\Controllers\LetterCategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\Authenticate;

$router->get('/', function () {
    return Response::make('', 302, ['Location' => '/dashboard']);
});

$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout'], [Authenticate::class]);

$router->get('/dashboard', [DashboardController::class, 'index'], [Authenticate::class]);
$router->get('/dashboard/chart-data', [DashboardController::class, 'chartData'], [Authenticate::class]);

$router->get('/incoming-letters', [IncomingLetterController::class, 'index'], [Authenticate::class]);
$router->post('/incoming-letters', [IncomingLetterController::class, 'store'], [Authenticate::class]);
$router->post('/incoming-letters/{id}/update', [IncomingLetterController::class, 'update'], [Authenticate::class]);
$router->post('/incoming-letters/{id}/delete', [IncomingLetterController::class, 'destroy'], [Authenticate::class]);
$router->get('/incoming-letters/{id}', [IncomingLetterController::class, 'show'], [Authenticate::class]);
$router->get('/incoming-letters/{id}/download', [IncomingLetterController::class, 'download'], [Authenticate::class]);

$router->get('/outgoing-letters', [OutgoingLetterController::class, 'index'], [Authenticate::class]);
$router->post('/outgoing-letters', [OutgoingLetterController::class, 'store'], [Authenticate::class]);
$router->post('/outgoing-letters/{id}/update', [OutgoingLetterController::class, 'update'], [Authenticate::class]);
$router->post('/outgoing-letters/{id}/delete', [OutgoingLetterController::class, 'destroy'], [Authenticate::class]);
$router->get('/outgoing-letters/{id}', [OutgoingLetterController::class, 'show'], [Authenticate::class]);
$router->get('/outgoing-letters/{id}/download', [OutgoingLetterController::class, 'download'], [Authenticate::class]);

$router->get('/letter-categories', [LetterCategoryController::class, 'index'], [Authenticate::class, AdminOnly::class]);
$router->post('/letter-categories', [LetterCategoryController::class, 'store'], [Authenticate::class, AdminOnly::class]);
$router->post('/letter-categories/{id}/delete', [LetterCategoryController::class, 'destroy'], [Authenticate::class, AdminOnly::class]);

$router->get('/reports', [ReportController::class, 'index'], [Authenticate::class]);
$router->get('/reports/export/pdf', [ReportController::class, 'exportPdf'], [Authenticate::class]);
$router->get('/reports/export/excel', [ReportController::class, 'exportExcel'], [Authenticate::class]);

$router->get('/users', [UserController::class, 'index'], [Authenticate::class, AdminOnly::class]);
$router->post('/users', [UserController::class, 'store'], [Authenticate::class, AdminOnly::class]);
$router->post('/users/{id}/update', [UserController::class, 'update'], [Authenticate::class, AdminOnly::class]);
$router->post('/users/{id}/reset', [UserController::class, 'resetPassword'], [Authenticate::class, AdminOnly::class]);
$router->post('/users/{id}/delete', [UserController::class, 'destroy'], [Authenticate::class, AdminOnly::class]);

$router->get('/settings', [SystemSettingController::class, 'index'], [Authenticate::class, AdminOnly::class]);
$router->post('/settings', [SystemSettingController::class, 'save'], [Authenticate::class, AdminOnly::class]);
$router->get('/settings/backup', [SystemSettingController::class, 'backup'], [Authenticate::class, AdminOnly::class]);
$router->post('/settings/restore', [SystemSettingController::class, 'restore'], [Authenticate::class, AdminOnly::class]);
