<?php

namespace App\Http\Controllers\Self_enrollment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;

use App\Imports\Self_enrollment\BroadpathUploadEnrollee;
use App\Imports\Self_enrollment\BroadpathUploadCancellation;
use App\Imports\Self_enrollment\BroadpathUploadForApproving;
use App\Exports\Self_enrollment\BroadpathExportEnrollee;

use App\Imports\Self_enrollment\LlibiUploadEnrollee;
use App\Exports\Self_enrollment\LlibiExportEnrollee;

use App\Imports\Self_enrollment\EigthByEigthUploadEnrollee;
use App\Exports\Self_enrollment\EigthByEigthExportEnrollee;
use App\Models\Self_enrollment\members;
use App\Models\Self_enrollment\contact;
use App\Models\Self_enrollment\attachment;
use App\Models\Self_enrollment\life_insurance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Self_enrollment\ManageBroadpathClients;
use App\Http\Controllers\Self_enrollment\ManageBroadpathNotifications;

use App\Http\Controllers\Self_enrollment\ManageEigthByEigthClients;
use App\Http\Controllers\Self_enrollment\ManageEigthByEigthNotifications;

use App\Http\Controllers\Self_enrollment\ManageLlibiClients;
use App\Http\Controllers\Self_enrollment\ManageLlibiNotifications;

use App\Http\Controllers\Self_enrollment\ManageDeelClients;
use App\Http\Controllers\Self_enrollment\ManageDeelNotifications;
use App\Http\Controllers\Self_enrollment\PreqinController;
use App\Imports\Self_enrollment\PreqinImportPrincipal;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ManageSelfEnrollmentController extends Controller
{
  //ADMIN
  public function getAllPrincipalClients($status, $company)
  {
    $list = DB::table('self_enrollment_members as t1')
      ->join('self_enrollment_contact as t2', 't1.id', '=', 't2.link_id')
      ->select(
        't1.id',
        't1.client_company',
        't1.upload_date',
        't1.plan',
        't1.mbl',
        't1.room_and_board',
        't1.member_id',
        't1.hash',
        't1.relation',
        't1.first_name',
        't1.last_name',
        't1.middle_name',
        't1.birth_date',
        't1.gender',
        't1.civil_status',
        't1.skip_hierarchy',
        't1.skip_reason',
        't1.skip_document',
        't1.hire_date',
        't1.end_date',
        't1.coverage_date',
        't1.certificate_no',
        't1.certificate_encode_datetime',
        't1.kyc',
        't1.kyc_timestamp',
        't1.with_er_card',
        't1.milestone',
        't1.form_locked',
        't1.status',

        't2.link_id',
        't2.street',
        't2.barangay',
        't2.city',
        't2.province',
        't2.zip_code',
        't2.email',
        't2.email2',
        't2.mobile_no',
      )
      ->where('t1.relation', 'PRINCIPAL')
      ->where('t1.client_company', $company)
      ->where('t1.status', $status)
      ->orderBy('t1.member_id', 'ASC')
      ->get();

    return array(
      'list' => $list,
    );
  }

  public function importClients($company, Request $request)
  {
    switch ($company) {
      case 'BROADPATH':
        $excel = Excel::import(new BroadpathUploadEnrollee($company), $request->file);
        break;
      case 'LLIBI':
        $excel = Excel::import(new LlibiUploadEnrollee($company), $request->file);
        break;
      case '8X8':
        $excel = Excel::import(new EigthByEigthUploadEnrollee($company), $request->file);
        break;
      case 'PREQIN':
        $excel = Excel::import(new PreqinImportPrincipal, $request->file);
        break;
    }

    return response()->noContent();
  }

  public function getSubmittedAndApprovedClients($memberid, $company)
  {
    switch ($company) {
      case 'PREQIN':
        $list = members::where('client_company', $company)
          ->where('member_id', $memberid)
          ->whereIn('status', [3, 6])
          ->get(['id', 'first_name', 'last_name', 'relation', 'certificate_no', 'certificate_encode_datetime', 'birth_date', 'status']);
        break;

      default:
        $list = members::where('client_company', $company)
          ->where('member_id', $memberid)
          ->whereIn('status', [2, 4])
          ->get(['id', 'first_name', 'last_name', 'relation', 'certificate_no', 'certificate_encode_datetime', 'birth_date', 'status']);
        break;
    }

    return array(
      'list' => $list,
    );
  }

  public function removeClient(Request $request)
  {
    $members = [
      'status' => $request->client_company == 'PREQIN' ? 4 : 3,
      'form_locked' => 1,
    ];
    members::where('member_id', $request->member_id)
      ->where('client_company', $request->client_company)
      ->update($members);
  }

  public function updateClient($company, Request $request)
  {
    switch ($company) {
      case 'BROADPATH':
        //$this->updateEnrolleeBroadPath($company, $request);
        (new ManageBroadpathClients)
          ->updateEnrollee($company, $request);
        break;
      case 'LLIBI':
        //$this->updateEnrolleeLlibi($company, $request);
        (new ManageLlibiClients)
          ->updateEnrollee($request);
        break;
      case '8X8':
        (new ManageEigthByEigthClients)
          ->updateEnrollee($company, $request);
        break;
      case 'PREQIN':
        (new PreqinController)
          ->updateEnrollee($company, $request);
        break;
    }
  }

  public function getAllClients($status, $company)
  {
    $list = DB::table('self_enrollment_members')
      ->where('client_company', $company)
      ->whereIn('status', [2, 4])
      ->orderBy('member_id', 'ASC')
      ->orderBy('id', 'ASC')
      ->get();

    return array(
      'list' => $list,
    );
  }

  public function exportClients($company)
  {
    $exists = members::where('status', 2)
      ->where('client_company', $company)
      ->exists();

    if ($exists) {
      $dateTime = date('YmdHis');
      $fileName = 'Enrollees-masterlist_' . $company . '_' . $dateTime . '.xlsx';

      switch ($company) {
        case 'BROADPATH':
          return Excel::download(new BroadpathExportEnrollee($company), $fileName);
        case 'LLIBI':
          return Excel::download(new LlibiExportEnrollee($company), $fileName);
        case '8X8':
          return Excel::download(new EigthByEigthExportEnrollee($company), $fileName);
      }
    }
  }

  public function importClientCancellation($company, Request $request)
  {
    //$comp = 'BROADPATH';
    switch ($company) {
      case 'BROADPATH':
        $excel = Excel::import(new BroadpathUploadCancellation($company), $request->file);
        break;
      case 'LLIBI':
        $excel = Excel::import(new LlibiUploadEnrollee($company), $request->file);
        break;
      case '8X8':
        $excel = Excel::import(new EigthByEigthUploadEnrollee($company), $request->file);
        break;
    }

    return response()->noContent();
  }

  public function importClientForApproving($company, Request $request)
  {
    //$comp = 'BROADPATH';
    switch ($company) {
      case 'BROADPATH':
        $excel = Excel::import(new BroadpathUploadForApproving($company), $request->file);
        break;
      case 'LLIBI':
        // $excel = Excel::import(new LlibiUploadEnrollee($company), $request->file);
        break;
      case '8X8':
        //$excel = Excel::import(new EigthByEigthUploadEnrollee($company), $request->file);
        break;
    }

    return response()->noContent();
  }

  public function testing()
  {

    $cars = array(
      array('model' => 'Civic', 'year' => 2018, 'brand' => 'Honda'),
      array('model' => 'Camry', 'year' => 2019, 'brand' => 'Toyota'),
      array('model' => 'Corolla', 'year' => 2021, 'brand' => 'Toyota'),
      array('model' => 'Sonata', 'year' => 2020, 'brand' => 'Hyundai'),
      array('model' => 'Elantra', 'year' => 2017, 'brand' => 'Hyundai'),
      array('model' => 'Fusion', 'year' => 2019, 'brand' => 'Ford'),
      array('model' => 'Mustang', 'year' => 2021, 'brand' => 'Ford'),
      array('model' => 'Impreza', 'year' => 2020, 'brand' => 'Subaru'),
      array('model' => 'Outback', 'year' => 2018, 'brand' => 'Subaru'),
      array('model' => 'X3', 'year' => 2019, 'brand' => 'BMW'),
      array('model' => 'X5', 'year' => 2021, 'brand' => 'BMW'),
      array('model' => 'A3', 'year' => 2020, 'brand' => 'Audi'),
      array('model' => 'A4', 'year' => 2019, 'brand' => 'Audi'),
      array('model' => 'Golf', 'year' => 2021, 'brand' => 'Volkswagen'),
      array('model' => 'Passat', 'year' => 2018, 'brand' => 'Volkswagen'),
      array('model' => 'C-Class', 'year' => 2020, 'brand' => 'Mercedes'),
      array('model' => 'E-Class', 'year' => 2019, 'brand' => 'Mercedes'),
      array('model' => 'S60', 'year' => 2021, 'brand' => 'Volvo'),
      array('model' => 'XC90', 'year' => 2018, 'brand' => 'Volvo')
    );

    // Count the occurrences of each brand
    $brandCount = array();
    foreach ($cars as $car) {
      $brand = $car['brand'];
      if (isset($brandCount[$brand])) {
        $brandCount[$brand]++;
      } else {
        $brandCount[$brand] = 1;
      }
    }

    // Find the last index of each brand
    $lastIndex = array();
    foreach ($cars as $index => $car) {
      $brand = $car['brand'];
      $lastIndex[$brand] = $index;
    }

    // Print the count of duplicated brands and car details
    foreach ($cars as $index => $car) {
      $brand = $car['brand'];
      $model = $car['model'];
      $year = $car['year'];

      echo "Model: $model, Year: $year, Brand: $brand";

      // Print count if it's a duplicate brand
      if ($index === $lastIndex[$brand]) {
        echo " (Count: {$brandCount[$brand]})";
      }

      echo "<br>";
    }
  }

  //CLIENT
  public function checkClient($id, $company)
  {
    $principal = [];
    $dependent = [];
    switch ($company) {
      case 'BROADPATH':
        $list = (new ManageBroadpathClients)
          ->checkClient($id);

        $principal = $list['principal'];
        $dependent = $list['dependent'];
        break;
      case 'LLIBI':
        $list = (new ManageLlibiClients)
          ->checkClient($id);

        $principal = $list['principal'];
        $dependent = $list['dependent'];
        break;
      case '8X8':
        $list = (new ManageEigthByEigthClients)
          ->checkClient($id);

        $principal = $list['principal'];
        $dependent = $list['dependent'];
        break;
      case 'PREQIN':
        $list = (new PreqinController)
          ->checkClient($id);

        $principal = $list['principal'];
        $dependent = $list['dependent'];
        break;
    }

    return array(
      'principal' => $principal,
      'dependent' => $dependent
    );
  }

  public function updateClientInfo($company, Request $request)
  {
    switch ($company) {
      case 'BROADPATH':
        (new ManageBroadpathClients)
          ->updateClientInfo($request);
        break;
      case 'LLIBI':
        (new ManageLlibiClients)
          ->updateClientInfo($request);
        break;
      case '8X8':
        (new ManageEigthByEigthClients)
          ->updateClientInfo($request);
        break;
      case 'PREQIN':
        (new PreqinController)
          ->updateClientInfo($request);
        break;
    }
  }

  public function submitDependent($company, Request $request)
  {
    switch ($company) {
      case 'BROADPATH':
        (new ManageBroadpathClients)
          ->submitDependent($request);
        break;
      case 'LLIBI':
        (new ManageLlibiClients)
          ->submitDependent($request);
        break;
      case '8X8':
        (new ManageEigthByEigthClients)
          ->submitDependent($request);
        break;
      case 'PREQIN':
        return (new PreqinController)->submitDependent($request);
        break;
    }
  }

  public function submitWithoutDependent($company, Request $request)
  {
    switch ($company) {
      case 'BROADPATH':
        (new ManageBroadpathClients)
          ->submitWithoutDependent($request);
        break;
      case 'LLIBI':
        (new ManageLlibiClients)
          ->submitWithoutDependent($request);
        break;
      case '8X8':
        (new ManageEigthByEigthClients)
          ->submitWithoutDependent($request);
        break;
      case 'PREQIN':
        (new PreqinController)
          ->submitWithoutDependent($request);
        break;
    }
  }

  //FILE UPLOADS
  public function getFiles($id)
  {
    $attachment = attachment::where('link_id', $id)
      ->select('id', 'file_name', 'file_link')
      ->get();

    return $attachment;
  }

  public function removeFile(Request $request)
  {
    attachment::where('id', $request->imageId)
      ->delete();

    if (count(attachment::where('link_id', $request->id)->get(['id'])) == 0)
      members::where('id', $request->id)
        ->update(['attachments' => '']);
  }

  public function checkReminders($company, $checkdate, $dateFinalWarning, $dateFormLocked)
  {
    switch ($company) {
      case 'BROADPATH':
        (new ManageBroadpathClients)
          ->checkReminders($checkdate, $dateFinalWarning, $dateFormLocked);
        break;
      case 'LLIBI':
        (new ManageLlibiClients)
          ->checkReminders($checkdate, $dateFinalWarning, $dateFormLocked);
        break;
      case '8X8':
        (new ManageEigthByEigthClients)
          ->checkReminders($checkdate, $dateFinalWarning, $dateFormLocked);
        break;
    }
  }

  //LIFE INSURANCE
  public function getClientsForLifeInsurance($status)
  {
    $list = DB::table('self_enrollment_members as t1')
      ->join('self_enrollment_contact as t2', 't1.id', '=', 't2.link_id')
      ->join('self_enrollment_life_insurance as t3', 't1.id', '=', 't3.link_id')
      ->select('t1.*', 't2.email', 't2.email2', 't2.mobile_no', 't2.street', 't2.barangay', 't2.city', 't2.province', 't2.zip_code', 't3.salary', 't3.insurance_no', 't3.insurance_encode_datetime', 't3.status as li_status')
      ->where('t1.relation', 'PRINCIPAL')
      ->where('t3.status', $status)
      ->orderBy('t1.id', 'ASC')
      ->get();

    return array(
      'list' => $list,
    );
  }

  public function updateLifeInsurance(Request $request)
  {
    if (!empty($request->insuranceNo)) {
      $members = [
        'insurance_no' => $request->insuranceNo,
        'insurance_encode_datetime' => date('Y-m-d H:i:s'),
        'status' => 1,
      ];
      life_insurance::where('link_id', $request->id)
        ->update($members);

      $info = [
        'name' => $request->firstName . ' ' . $request->lastName,
        'email' => $request->email,
        'mobile' => $request->mobile,
        'insSms' => '',
        'insMail' => ''
      ];

      (new ManageEigthByEigthNotifications)
        ->approvedWithLifeInsuranceNo($info);
    }
  }

  public function denyLifeInsurance(Request $request)
  {
    $members = [
      'status' => 3,
      'form_locked' => 1,
    ];
    members::where('member_id', $request->member_id)
      ->where('client_company', $request->client_company)
      ->update($members);
  }

  //MILESTONE
  public function milestoneUpdate($company, Request $request)
  {

    members::where('id', $request->id)
      ->update(['milestone' => $request->milestone]);

    if ($request->milestone == 1)
      members::where('member_id', $request->memberId)
        ->where('relation', '!=', 'PRINCIPAL')
        ->where('status', 2)
        ->update(['status' => 11]); //temp

    if ($request->milestone == 2)
      members::where('member_id', $request->memberId)
        ->whereIn('relation', ['PARENT', 'SPOUSE'])
        ->update(['status' => 12]); //cancel because of new civil status

  }

  // public function newEnrollment(Request $request)
  // {
  //   $oid = $request->oid;
  //   $birth_date = Carbon::parse($request->birthdate)->format('Y-m-d');
  //   $attachment = $request->file('attachment');

  //   $checkIfExistPrincipal = members::query()->where(['member_id' => $request->oid, 'birth_date' => $birth_date])->first();

  //   if ($checkIfExistPrincipal) {
  //     return response()->json(['message' => 'Already Enrolled', 'enrollee' => $checkIfExistPrincipal], 400);
  //   }

  //   $enrollee = members::create(
  //     [
  //       'client_company' => 'BROADPATH',
  //       'member_id' => $request->oid,
  //       'hash' => Str::uuid(),
  //       'relation' => $request->relation,
  //       'first_name' => $request->firstname,
  //       'last_name' => $request->lastname,
  //       'middle_name' => $request->middlename,
  //       'birth_date' => $birth_date,
  //       'gender' => $request->gender,
  //       'civil_status' => $request->civilstatus,
  //       'hire_date' => Carbon::now(),
  //       'coverage_date' => Carbon::now()->addYear(),
  //       'form_locked' => 0,
  //       'status' => 1,
  //     ]
  //   );

  //   foreach ($attachment as $key => $attch) {
  //     $path = $attch->storeAs('Self_enrollment/Broadpath/' . $request->oid, $attch->getClientOriginalName(), 'public');
  //     $file_name = $attch->getClientOriginalName();

  //     attachment::create([
  //       'link_id' => $enrollee->id,
  //       'file_name' => $file_name,
  //       'file_link' => $path
  //     ]);

  //     members::where('id', $enrollee->id)->update(['attachments' => 1]);
  //   }


  //   return response()->json(['message' => 'Successfully save new enroll', 'enrollee' => $enrollee]);
  // }

  // public function updateEnrollment(Request $request, $id)
  // {
  //   $oid = $request->oid;
  //   $birth_date = Carbon::parse($request->birthdate)->format('Y-m-d');
  //   $attachment = $request->file('attachment');

  //   $enrollee = members::where('id', $id)->update(
  //     [
  //       'member_id' => $oid,
  //       'relation' => $request->relation,
  //       'first_name' => $request->firstname,
  //       'last_name' => $request->lastname,
  //       'middle_name' => $request->middlename,
  //       'birth_date' => $birth_date,
  //       'gender' => $request->gender,
  //       'civil_status' => $request->civilstatus,
  //     ]
  //   );

  //   attachment::where('link_id', $id)->delete();
  //   foreach ($attachment as $key => $attch) {
  //     $path = $attch->storeAs('Self_enrollment/Broadpath/' . $request->oid, $attch->getClientOriginalName(), 'public');
  //     $file_name = $attch->getClientOriginalName();

  //     attachment::create([
  //       'link_id' => $id,
  //       'file_name' => $file_name,
  //       'file_link' => $path
  //     ]);

  //     members::where('id', $id)->update(['attachments' => ++$key]);
  //   }


  //   return response()->json(['message' => 'Successfully save new enroll', 'enrollee' => members::where('id', $id)->get()]);
  // }

  // public function fetchPrincipal()
  // {
  //   return members::where('relation', 'PRINCIPAL')->select(
  //     'id',
  //     'member_id',
  //     'relation',
  //     'first_name',
  //     'last_name',
  //     'middle_name',
  //     'birth_date',
  //     'gender',
  //     'civil_status',
  //   )
  //     ->take(100)
  //     ->get();
  // }
}
