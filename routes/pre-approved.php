<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PreApprove\UtilizationController;
use App\Http\Controllers\PreApprove\LaboratoryController;
use App\Http\Controllers\PreApprove\PreApprovedController;
use App\Http\Controllers\PreApprove\PreApprovedLogsController;

Route::get('/pre-approve/utilization', [UtilizationController::class, 'index']);
Route::post('/pre-approve/utilization', [UtilizationController::class, 'importUtilization']);
Route::post('/pre-approve/deel/upload', [UtilizationController::class, 'importDeelUpload']);

Route::get('/pre-approve/get-employees', [PreApprovedController::class, 'getEmployee']);


Route::get('/pre-approve/laboratory/export', [LaboratoryController::class, 'export']);
Route::post('/pre-approve/laboratory/import', [LaboratoryController::class, 'import']);
Route::get('/pre-approve/laboratory', [LaboratoryController::class, 'index']);
Route::post('/pre-approve/laboratory', [LaboratoryController::class, 'store']);
Route::put('/pre-approve/laboratory/{id}', [LaboratoryController::class, 'update']);
Route::delete('/pre-approve/laboratory/{id}', [LaboratoryController::class, 'destroy']);

Route::apiResource('/pre-approve-logs', PreApprovedLogsController::class);
