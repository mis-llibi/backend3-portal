<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
use App\Http\Controllers\Self_service\AdminController as SelfService;
use App\Http\Controllers\Self_service\ProviderController as ProviderSelfService;

use App\Http\Controllers\Self_service\AutoSendPendingNotMoving;
use App\Http\Controllers\SettingController;
use App\Models\User;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\PreApprove\UtilizationController;
use App\Http\Controllers\PreApprove\LaboratoryController;

use App\Http\Controllers\Api_third_party\MobileApiAccessController;
use App\Http\Controllers\Feedback\FeedbackCorporateController;
use App\Http\Controllers\SearchMasterlist\MasterlistController;
use App\Http\Controllers\Self_service\ComplaintController;

use App\Http\Controllers\Api_third_party\BenadEncryptor;
use App\Http\Controllers\ClientPortalErrorLogsController;
use App\Http\Controllers\EmailProviderSettingController;
use App\Mail\MailerSendTest;
use App\Services\SendingEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\MailerSend;

use App\Http\Controllers\Callback_Request;
use App\Http\Controllers\Callback_Request\CallbackRequest;

Route::group(['middleware' => ['auth:sanctum']], function () {

  Route::get('/user', function (Request $request) {
    if ($request->user()->is_logout == 0) {
      User::where(['id' => $request->user()->id])->update(['is_logout' => 1]);

      $request->session()->invalidate();

      $request->session()->regenerateToken();

      return response()->json([
        'message' => 'account is inactive'
      ], 404);
    }
    if ($request->user()->status == 1) return $request->user();
    return response()->json([
      'message' => 'account is inactive'
    ], 404);
  });

  //HR ENROLLMENT
  Route::get('/get-enrollees/{status}', [ManageEnrolleeController::class, 'getEnrollees']);
  Route::get('/get-correction/{id}', [ManageEnrolleeController::class, 'getCorrection']);

  //SELF ENROLLMENT ADMIN
  Route::get('/self-enrollment/get-submitted-and-approved-clients/{memberid}/{company}', [SelfEnrollmentController::class, 'getSubmittedAndApprovedClients']);
  Route::get('/self-enrollment/get-all-principal-clients/{status}/{company}', [SelfEnrollmentController::class, 'getAllPrincipalClients']);
  Route::get('/self-enrollment/export-clients/{company}', [SelfEnrollmentController::class, 'exportClients']);

  //SELF ENROLLMENT FOR LIFE INSURANCE
  Route::get('/self-enrollment/get-clients-for-life-insurance/{status}', [SelfEnrollmentController::class, 'getClientsForLifeInsurance']);
});

Route::get('/self-enrollment/testing', [SelfEnrollmentController::class, 'testing']);

//SELF ENROLLMENT CLIENTS
Route::get('/self-enrollment/get-client-info/{id}/{company}', [SelfEnrollmentController::class, 'checkClient']);
Route::get('/self-enrollment/get-uploaded-files/{id}', [SelfEnrollmentController::class, 'getFiles']);
Route::get('/self-enrollment/check-reminders/{company}/{date}/{finalWarning}/{locked}', [SelfEnrollmentController::class, 'checkReminders']);

//SELF-SERVICE
Route::get('/self-service/download-pdf', [SelfService::class, 'encryptPdf']);
Route::get('/self-service/get-uploaded-files/{id}', [SelfService::class, 'getFiles']);
Route::get('/self-service/admin-search-request/{search}/{id}', [SelfService::class, 'SearchRequest']);
Route::get('/self-service/admin-search-request/pending-counter', [SelfService::class, 'pendingCounter']);
Route::post('/self-service/admin/export', [SelfService::class, 'export']);
Route::post('/self-service/admin/export-records', [SelfService::class, 'exportRecords']);
Route::post('/self-service/admin/preview-export-records', [SelfService::class, 'previewExport']);
Route::get('/self-service/admin/update-tat', [SelfService::class, 'updateTAT']);


Route::get('/provider-search-request/{search}/{id}', [ProviderSelfService::class, 'SearchRequest']);

//MOBILE SELF-SERVICE LOGIN
// Route::get('/user-mobile', [MobileAuthenticatedSessionController::class, 'checkSession']);

//DENTAL INSURANCE
Route::get('/dental-insurance/member', [DentalInsuranceAuthController::class, 'checkSession']);
Route::get('/dental-insurance/{enrolled}', [DentalInsuranceManageMemberController::class, 'viewMembers']);

//DA EXTRACT
Route::get('/da-extract-members', [ManageDaMemberController::class, 'getMembers']);

//CORPORATE API
Route::get('/corporate/employees/{status}/{lastname}/{firstname}', [CorporateMembers::class, 'GetEmployees']);
Route::get('/corporate/dependents/{status}/{lastname}/{firstname}', [CorporateMembers::class, 'GetDependents']);

//HOSPITAL
Route::get('/hospitals', [CallbackRequest::class, 'getHospitals']);
Route::get('/getMasterlist', [CallbackRequest::class, 'getMasterlist']);
Route::post('/submitCallback', [CallbackRequest::class, 'submitCallback']);
Route::post('/changeCallbackStatus', [CallbackRequest::class, 'changeCallbackStatus']);
Route::post('/doneStatusCallback', [CallbackRequest::class, 'doneStatusCallback']);


//COMPANIES SYNC API
require __DIR__ . '/ebd.php';


Route::get('/create-client-portal-account', [AuthenticatedSessionController::class, 'createUser']);
Route::post('/update-client-portal-account', [AuthenticatedSessionController::class, 'updateUser']);
Route::get('/auto-send', [AutoSendPendingNotMoving::class, 'autoSendEmail']);
Route::post('/view-by', [SelfService::class, 'viewBy']);
Route::get('/view-logs', [SelfService::class, 'viewLogs']);
Route::get('/settings', [SettingController::class, 'index']);
Route::put('/settings', [SettingController::class, 'update']);

require __DIR__ . '/feedback.php';
require __DIR__ . '/pre-approved.php';

//THIRD PARTY APP API
Route::get('/third-party/member', [MobileApiAccessController::class, 'GetMemberData']);
Route::post('/renew/upload-csv', [SelfService::class, 'renewImportCsv']);
Route::get('/environment-checker', fn () => config('app.env'));
Route::get('/search-masterlist', [MasterlistController::class, 'index']);
Route::get('/export-enrolled', [ManageEnrolleeController::class, 'exportEnrolled']);
Route::post('/upload-file', [ManageEnrolleeController::class, 'uploadFile']);
Route::get('/retrieve-file', [ManageEnrolleeController::class, 'retrieveFile']);


Route::controller(ComplaintController::class)->group(function () {
  Route::get('/complaint', 'index');
  Route::get('/complaint/{id}', 'show');
  Route::post('/complaint', 'store');
});

require __DIR__ . '/hris.php';

Route::get('/excel-template', [ManageEnrolleeController::class, 'excelTemplate']);


Route::get('/mailersend', function () {
  // Mail::to('glenilagan@llibi.com')->send(new MailerSendTest);

  // $mailersend = new MailerSend(['api_key' => env('MAILERSEND_API_KEY')]);

  // $recipients = [
  //   new Recipient('glenilagan@llibi.com', ''),
  // ];

  // $emailParams = (new EmailParams())
  //   ->setFrom(env('MAILERSEND_API_ADDRESS_FROM'))
  //   ->setFromName(env('MAILERSEND_API_ADDRESS_FROMNAME'))
  //   ->setRecipients($recipients)
  //   ->setSubject('CLIENT CARE PORTAL')
  //   ->setHtml(view('test-mailer', ['data' => 'glennmore'])->render())
  //   ->setAttachments(['https://llibi-storage.sgp1.cdn.digitaloceanspaces.com/eportal/masterlist-report-every-monday/2024/Apr/masterlist-member_1712548806_1.xlsx']);

  // $mailersend->email->send($emailParams);

  // $body = view('test-mailer', ['data' => 'glennmore'])->render();

  $body = view('test-mailer', ['data' => 'glennmore'])->render();
  $email = ['glenilagan@llibi.com', 'glenilagan@llibi.com'];
  $subject = 'CLIENT CARE PORTAL';
  $cc = ['test123@yopmail.com', 'test1123@yopmail.com'];
  $bcc = ['test1234@yopmail.com', 'test12341@yopmail.com'];
  $attachments = ['https://llibi-storage.sgp1.cdn.digitaloceanspaces.com/eportal/masterlist-report-every-monday/2024/Apr/masterlist-member_1712548806_1.xlsx'];

  $sending = new SendingEmail();
  $sending->sendMailerSendProvider($body, $email, $subject, $cc, $bcc, $attachments);
});

Route::post('/provider-setting', EmailProviderSettingController::class)->middleware('throttle:5,1');

Route::controller(ClientPortalErrorLogsController::class)->group(function () {
  Route::get('/error-logs', 'index');
  Route::post('/error-logs', 'store');
  Route::post('/error-logs-send-notify', 'sendNotify');
});

require __DIR__ . '/test-route.php';
require __DIR__ . '/byron.php';
