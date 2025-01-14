<?php

use App\Http\Controllers\Members\AdminController;
use App\Http\Controllers\Members\DependentController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Members\ManageEnrolleeController;
use App\Http\Controllers\Members\PrincipalController;
use App\Http\Controllers\Members\MilestoneController;
use App\Http\Controllers\Members\PendingDocumentController;

Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::controller(ManageEnrolleeController::class)
    ->prefix('members-enrollment')
    ->group(function () {
      Route::post('/submit-for-enrollment', 'submitForEnrollment');
      Route::delete('/delete-pending/{id}', 'deletePending');
      Route::post('/new-enrollment', 'newEnrollment');
      Route::put('/new-enrollment/{id}', 'updateEnrollment');
      Route::post('/submit-for-deletion', 'submitForDeletion');
      Route::get('/principals', 'fetchPrincipal');
      Route::get('/members', 'index');

      Route::patch('/change-plan/{id}', 'changePlan');
      Route::post('/delete-members', 'deleteMember');
      Route::post('/update-information', 'updateInformation');
    });

  Route::controller(PrincipalController::class)
    ->prefix('members-enrollment')
    ->group(function () {
      Route::get('/principals', 'fetchPrincipal');
    });

  Route::controller(DependentController::class)
    ->prefix('members-enrollment')
    ->group(function () {
      Route::get('/view-dependents', 'getDependents');
    });

  Route::controller(MilestoneController::class)
    ->prefix('members-enrollment')
    ->group(function () {
      Route::get('/dependents-for-inactive', 'getDependentsForInactive');
      Route::post('/dependents-for-inactive', 'submitDependentsForInactive');
    });

  Route::controller(PendingDocumentController::class)
    ->prefix('members-enrollment')
    ->group(function () {
      Route::get('/pending-documents/{member_id}', 'pendingDocuments');
      Route::post('/upload-pending-documents', 'uploadPendingDocuments');
    });
});

Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::controller(AdminController::class)
    ->prefix('admin')
    ->group(function () {
      Route::patch('/approve-members/{id}', 'approveMember');
      Route::patch('/approve-deletion/{id}', 'approveDeletion');
      Route::patch('/approve-change-plan/{id}', 'approveChangePlan');
      Route::patch('/disapprove-member/{id}', 'disapproveMember');
      Route::patch('/approve-edit-information/{id}', 'approveEditInformation');

      Route::post('/pending-documents', 'requestPendingDocuments');
    });
});
