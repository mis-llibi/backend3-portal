<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\Members\ManageMemberController;
use App\Http\Controllers\Members\ManageEnrolleeController;
use App\Http\Controllers\Self_service_mobile\MobileAuthenticatedSessionController;

use App\Http\Controllers\Self_enrollment\ManageSelfEnrollmentController as SelfEnrollmentController;

use App\Http\Controllers\Dental_insurance\DentalInsuranceAuthController;

use App\Http\Controllers\Dental_insurance\DentalInsuranceManageMemberController;

use App\Http\Controllers\Da_extract\ManageDaMemberController;

use App\Http\Controllers\Corporate\MembersController as CorporateMembers;

Route::group(['middleware' => ['auth:sanctum']], function () {

  Route::get('/user', function (Request $request) {
      if ($request->user()->status == 1) return $request->user();
      return response()->json([
        'message' => 'account is inactive'
      ], 404);
  });

  //HR ENROLLMENT
  Route::get('/get-enrollees/{status}', [ManageEnrolleeController::class, 'getEnrollees']);
  Route::get('/get-correction/{id}', [ManageEnrolleeController::class, 'getCorrection']);

  //SELF ENROLLMENT
  Route::get('/self-enrollment/get-client-for-approval/{memberid}/{company}', [SelfEnrollmentController::class, 'getApprovals']);

  Route::get('/self-enrollment/get-enrollees/{status}/{company}', [SelfEnrollmentController::class, 'getEnrollees']);
  Route::get('/self-enrollment/export-enrollees/{company}', [SelfEnrollmentController::class, 'exportEnrollees']);

});

//Self Enrollment Client Checker
Route::get('/self-enrollment/get-client-info/{id}/{company}', [SelfEnrollmentController::class, 'checkClient']);
Route::get('/self-enrollment/get-uploaded-files/{id}', [SelfEnrollmentController::class, 'getFiles']);
Route::get('/self-enrollment/check-reminder/{company}/{date}/{finalWarning}/{locked}', [SelfEnrollmentController::class, 'checkReminders']);

//Mobile Self Service Login
Route::get('/user-mobile', [MobileAuthenticatedSessionController::class, 'checkSession']);

//Dental Insurance Member
Route::get('/dental-insurance/member', [DentalInsuranceAuthController::class, 'checkSession']);
Route::get('/dental-insurance/{enrolled}', [DentalInsuranceManageMemberController::class, 'viewMembers']);

//DA Extraction
Route::get('/da-extract-members', [ManageDaMemberController::class, 'getMembers']);

//Corporate API
Route::get('/corporate/employees/{lastname}', [CorporateMembers::class, 'GetEmployees']);
Route::get('/corporate/dependents/{lastname}', [CorporateMembers::class, 'GetDependents']);

