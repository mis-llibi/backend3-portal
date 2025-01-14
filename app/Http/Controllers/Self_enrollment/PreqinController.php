<?php

namespace App\Http\Controllers\Self_enrollment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\NotificationStatus;
use App\Models\Self_enrollment\members;
use App\Models\Self_enrollment\contact;
use App\Models\Self_enrollment\attachment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PreqinController extends Controller
{
  //CLIENT
  public function checkClient($id)
  {
    $principal = members::where('hash', $id)
      ->where('client_company', 'PREQIN')
      ->where('relation', 'PRINCIPAL')
      ->select('id', 'member_id', 'last_name', 'first_name', 'middle_name', 'relation', 'birth_date', 'gender', 'civil_status', 'hire_date', 'coverage_date', 'mbl', 'room_and_board', 'status', 'form_locked')
      ->orderBy('id', 'DESC')
      ->limit(1)
      ->get();

    $dependent = members::where('member_id', (count($principal) > 0 ? $principal[0]->member_id : 'xxxxx'))
      ->where('client_company', 'PREQIN')
      ->where('relation', '!=', 'PRINCIPAL')
      ->where('status', 3)
      ->select('id as mId', 'last_name', 'first_name', 'middle_name', 'relation', 'birth_date', 'gender', 'civil_status', 'attachments')
      ->get();

    return array(
      'principal' => $principal,
      'dependent' => $dependent
    );
  }

  public function updateClientInfo(Request $request)
  {
    $update = [
      'first_name' => $this->clean($request->first_name),
      'middle_name' => $this->clean($request->middle_name),
      'last_name' => $this->clean($request->last_name),
      'civil_status' => strtoupper($request->civil_status),
      'gender' => strtoupper($request->gender),
      'birth_date' => $request->birth_date,
      'status' => 2,
    ];
    $member = members::where('id', $request->id)
      ->update($update);

    $updateContact = [
      'street' => $this->clean($request->street),
      'barangay' => $this->clean($request->barangay),
      'city' => $this->clean($request->city),
      'province' => $this->clean($request->province),
      'zip_code' => $request->zipCode,
      'mobile_no' => $request->mobile_number,

    ];
    $contact = contact::where('link_id', $request->id)
      ->update($updateContact);
  }

  public function clean($text)
  {
    return str_replace('ñ', 'Ñ', strtoupper($text));
  }

  public function submitDependent(Request $request)
  {
    /* 
      status 2 => fillout the personal details
      status 3 => submitted
      status 4 => submitted without dependets, the status of dependents will be 4 and the principal status 3
      status 6 => input cert no.
    */

    //Check attachment first before continuing to processing the data
    $rules = [];
    $att = [];
    for ($i = 0; $i < count($request->list); $i++) {
      if ($request->hasfile("attachment$i")) {
        foreach ($request->file("attachment$i") as $key => $file) {
          $rules["dependent_$i" . "_with_attachment_$key"] = 'mimes:jpg,jpeg,bmp,png,gif,svg,pdf';
          $att["dependent_$i" . "_with_attachment_$key"] = $file;
        }
      }
    }

    $validator = Validator::make($att, $rules);

    if ($validator->fails()) {
      return response()->json(array(
        'success' => false,
        'errors' => $validator->getMessageBag()->toArray(),
        'message' => 'Attachment/s must be an image or pdf only!'
      ), 400);
    }

    $update = [
      'civil_status' => $request->principalCivilStatus,
      'gender' => $request->genderPrincipal,
      'status' => 3,
    ];
    $member = members::where('id', $request->principalId)
      ->update($update);

    //update first the dependent to remove the unlisted for enrollment
    $dependent = members::where('relation', '!=', 'PRINCIPAL')
      ->where('member_id', $request->memberId)
      ->where('status', 2)
      ->update(['status' => 3]);

    $upMember = members::where('id', $request->principalId)
      ->where('status', 3)
      ->select(['id', 'member_id', 'last_name', 'first_name', 'mbl'])
      ->first();

    $upContact = contact::where('link_id', $request->principalId)->first();

    $arr = [];
    //breakdown of all dependents for enrollment
    for ($i = 0; $i < count($request->list); $i++) {
      $arr['client_company'] = 'PREQIN';
      $arr['member_id'] = $request->memberId;
      $arr['hire_date'] = $request->hireDate;
      $arr['coverage_date'] = $request->coverageDate;

      $arr['first_name'] = $this->clean($request->first_name[$i]);
      $arr['last_name'] = $this->clean($request->last_name[$i]);
      $arr['middle_name'] = $this->clean($request->middle_name[$i]);
      $arr['relation'] = strtoupper($request->relation[$i]);
      $arr['birth_date'] = $request->birth_date[$i];
      $arr['gender'] = strtoupper($request->gender[$i]);
      $arr['civil_status'] = strtoupper($request->civil_status[$i]);
      $arr['status'] = 3;

      //add or update dependent information
      if (isset($request->id[$i]) && (string)$request->id[$i] != 'undefined') {
        members::where('id', $request->id[$i])->update($arr);
        $id = $request->id[$i];
      } else {
        $member = members::create($arr);
        $id = $member->id;
      }

      //check every dependents if they have attachments
      if ($request->hasfile("attachment$i")) {
        foreach ($request->file("attachment$i") as $key => $file) {
          $path = $file->storeAs('Self_enrollment/PREQIN/' . $request->memberId, $file->getClientOriginalName(), 'public');
          $name = $file->getClientOriginalName();

          attachment::create([
            'link_id' => $id,
            'file_name' => $name,
            'file_link' => $path
          ]);

          members::where('id', $id)
            ->update(['attachments' => 1]);
        }
      }
    }

    $info = [
      'name' => $upMember->last_name . ', ' . $upMember->first_name,
      'email' => $upContact->email,
      'email2' => $upContact->email2,
      'mobile' => $upContact->mobile_no,
    ];

    (new PreqinNotificationController)->submittedWithDep($info);

    return response()->json(['message' => 'success'], 201);
  }

  public function submitWithoutDependent(Request $request)
  {
    /* 
      status 2 => fillout the personal details
      status 3 => submitted
      status 4 => submitted without dependets || the status of dependents will be 4 and the principal status 3
      status 6 => input cert no.
    */

    $update = [
      'civil_status' => $request->civilStatus,
      'gender' => $request->gender,
      'status' => 3,
    ];
    $principal = members::where('id', $request->id)
      ->update($update);

    $dependent = members::where('relation', '!=', 'PRINCIPAL')
      ->where('member_id', $request->memberId)
      ->where('status', 2)
      ->update(['status' => 4]);

    $upMember = members::where('id', $request->id)
      ->where('status', 3)
      ->select(['id', 'last_name', 'first_name'])
      ->first();

    $upContact = contact::where('link_id', $upMember->id)->first();

    $name = $upMember->last_name . ', ' . $upMember->first_name;

    $info = [
      'name' => $name,
      'email' => $upContact->email,
      'email2' => $upContact->email2,
      'mobile' => $upContact->mobile_no,
    ];

    (new PreqinNotificationController)->submittedWithoutDep($info);

    return response()->noContent(201);
  }

  public function updateEnrollee($company, $request)
  {
    // status 6 => input cert no.

    $list = (new ManageSelfEnrollmentController)->getSubmittedAndApprovedClients($request->empno, $company)['list'];

    foreach ($list as $key => $value) {
      $members = [
        'certificate_no' => (!empty($request->{'certificateNo' . $value->id}) ? $request->{'certificateNo' . $value->id} : ''),
        'certificate_encode_datetime' => (!empty($request->{'certificateNo' . $value->id}) ? date('Y-m-d H:i:s') : ''),
        'status' => 6,
      ];

      members::where('id', $value->id)->update($members);
    }

    $body = $this->getNotificationBodyListOfApprovedClient($request->empno, $company);

    $info = [
      'name' => $request->firstName . ' ' . $request->lastName,
      'email' => $request->email,
      'email2' => $request->altEmail,
      'mobile' => $request->mobile,
      'insSms' => $body['sms'],
      'insMail' => $body['mail']
    ];
    // Log::info($info);
    (new PreqinNotificationController)->approvedWithCertificateNo($info);
  }

  function getNotificationBodyListOfApprovedClient($empno, $company)
  {
    $list = (new ManageSelfEnrollmentController)->getSubmittedAndApprovedClients($empno, $company)['list'];

    $insSms = [];
    $insMail = [];
    foreach ($list as $key => $value) {
      $certNo = (!empty($value->certificate_no) ? $value->certificate_no : 'X');
      $insSms[] =
        ($value->relation == "PRINCIPAL" ? "P-" : "D-") . ucwords(strtolower($value->last_name . ', ' . $value->first_name)) . ' / ' . $certNo;

      $insMail[] = [
        'relation' => $value->relation,
        'last_name' => $value->last_name,
        'first_name' => $value->first_name,
        'middle_name' => $value->middle_name,
        'certNo' => $certNo,
      ];
    }

    return array(
      'sms' => $insSms,
      'mail' => $insMail
    );
  }
}
