<?php

namespace App\Http\Controllers\Members;

use App\Enums\Broadpath\Members\StatusEnum;
use App\Exports\Members\LateEnrolledExport;
use App\Exports\Members\PendingForSubmissionExport;
use App\Exports\Members\PhilcareMemberExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\NewEnrollmentRequest;
use App\Http\Requests\Member\UpdateInformationRequest;
use Illuminate\Http\Request;

use App\Imports\Members\MasterlistImport;
use App\Models\Members\DeletionAttachment;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Members\hr_members;
use App\Models\Members\hr_contact;
use App\Models\Members\hr_philhealth;

use App\Models\Members\hr_members_correction;
use App\Models\Members\hr_contact_correction;
use App\Models\Members\hr_philhealth_correction;
use App\Models\Members\HrMemberAttachment;
use App\Models\Members\HrMemberChangePlanCorrection;
use App\Models\Self_enrollment\attachment;
use App\Models\Self_enrollment\members;
use App\Services\SendingEmail;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManageEnrolleeController extends Controller
{
  public function index()
  {
    /**
     * 1 pending submission
     * 3 Pending deletion
     * 5 pending correction
     * 8 pending change plan
     */

    /**
     * 4 approved/active members
     * 6 approved correction
     * 9 approved change plan
     * 10 disapproved member
     */

    $status = request()->query('status');
    $search = request()->query('search');

    $members =  hr_members::query();
    $members = match ($status) {
      '1' => $members->pendingSubmission(),
      '3' => $members->pendingDeletion(),
      '5' => $members->pendingCorrection(),
      '6' => $members->approvedCorrection(),
      '7' => $members->deletedMember(),
      '8' => $members->pendingChangePlan(),
      '9' => $members->approvedChangePlan(),
      '10' => $members->disapprovedMember(),
      'approved-members' => $members->approvedMembers(),
      'approved-members-with-pending' => $members->activeMembersWithPending(),
      'all-pending' => $members->pendingApproval(),
      'pending-documents' => $members->pendingDocuments(),
      default => throw new Exception("Status not supported", 400),
    };

    $members = $members->with([
      'changePlanPending:id,member_link_id,plan',
      'contact',
      'correction',
      'contactCorrection'
    ]);

    if ($search) {
      $members = $members->where(function ($query) use ($search) {
        $query->where('first_name', 'LIKE', "%$search%");
        $query->orWhere('last_name', 'LIKE', "%$search%");
      });
    }

    if ($status == 7) {
      $members = $members->orderByDesc('approved_deleted_member_at');
    } else {
      $members = $members->orderByDesc('id');
    }

    $members = $members->get();

    return $members;
  }

  public function getEnrollees($status)
  {
    $list = DB::table('hr_members as t1')
      ->join('hr_contact as t2', 't1.id', '=', 't2.link_id')
      ->join('hr_philhealth as t3', 't1.id', '=', 't3.link_id')
      ->select('t1.id', 't1.*', 't2.*', 't3.*')
      ->where('t1.status', $status)
      ->orderBy('t1.id', 'DESC')
      ->get();

    $pending = hr_members::where('status', 1)->get();
    $for_enrollment = hr_members::where('status', 2)->get();
    $enrolled = hr_members::where('status', 4)->get();
    $denied = hr_members::where('status', 6)->get();
    $for_deletion = hr_members::where('status', 3)->get();
    $for_correction = hr_members::where('status', 7)->get();
    $for_cancellation = hr_members::where('status', 8)->get();
    $cancelled = hr_members::where('status', 9)->get();

    $new_enrollee = members::query()->where(['status' => 1, 'client_company' => auth()->user()->company_id])->latest()->get();

    //$pending = array_count_values(array_column($members, 'status'))[$pending];
    return array(
      'list' => $list,
      'pending' => count($pending),
      'for_enrollment' => count($for_enrollment),
      'enrolled' => count($enrolled),
      'denied' => count($denied),
      'for_deletion' => count($for_deletion),
      'for_correction' => count($for_correction),
      'for_cancellation' => count($for_cancellation),
      'cancelled' => count($cancelled),
      'new_enrollee' => $new_enrollee,
    );
  }

  public function import(Request $request)
  {
    $comp = '';
    $import = new MasterlistImport($comp);
    $excel = Excel::import($import, $request->file);
    $late_enrolled = $import->getLateEnrolledImport();

    $sending = new SendingEmail(
      email: 'testllibi1@yopmail.com',
      body: view('send-late-enrollee-hr-portal', ['data' => $late_enrolled]),
      subject: 'LATE ENROLLEE - NOTIFICATION',
      attachments: [],
    );
    $sending->send();


    return response()->json($late_enrolled);
  }

  public function create(Request $request)
  {
    $members = [
      'employee_no' => $request->empno,
      'last_name' => strtolower($request->lastName),
      'first_name' => strtolower($request->firstName),
      'middle_name' => strtolower($request->middleName),
      'extension' => strtoupper($request->extension),
      'gender' => strtoupper($request->gender),
      'member_type' => strtoupper($request->memberType),
      'birth_date' => $request->birthDate,
      'relationship_id' => $request->relationshipId,
      'civil_status' => $request->civilStatus,
      'effective_date' => $request->effectiveDate,
      'date_hired' => $request->dateHired,
      'reg_date' => $request->regDate,
      'if_enrollee_is_a_philhealth_member' => strtoupper($request->philHealthMember),
      'client_remarks' => $request->clientRemarks,
      // status 101 means late enrollee
      'status' => Carbon::parse($request->dateHired)->diffInDays(now()) > 30 ? 101 : 1,
      'late_enrolled_remarks' => $request->lapseRemarks,
    ];
    $member = hr_members::create($members);

    $contact = [
      'link_id' => $member->id,
      'street' => $request->street,
      'city' => $request->city,
      'province' => $request->province,
      'zip_code' => $request->zipCode,
      'email' => $request->email,
      'mobile_no' => $request->mobile,
    ];
    hr_contact::create($contact);

    $philHealth = [
      'link_id' => $member->id,
      'philhealth_conditions' => $request->philHealthConditions,
      'position' => $request->position,
      'plan_type' => $request->planType,
      'branch_name' => $request->branchName,
      'philhealth_no' => $request->philHealthNo,
      'senior_citizen_id_no' => $request->seniorCitizenIDNo,
    ];
    hr_philhealth::create($philHealth);
  }

  public function update(Request $request)
  {
    $members = [
      'employee_no' => $request->empno,
      'last_name' => strtolower($request->lastName),
      'first_name' => strtolower($request->firstName),
      'middle_name' => strtolower($request->middleName),
      'extension' => strtoupper($request->extension),
      'gender' => strtoupper($request->gender),
      'member_type' => strtoupper($request->memberType),
      'birth_date' => $request->birthDate,
      'relationship_id' => $request->relationshipId,
      'civil_status' => $request->civilStatus,
      'effective_date' => $request->effectiveDate,
      'date_hired' => $request->dateHired,
      'reg_date' => $request->regDate,
      'if_enrollee_is_a_philhealth_member' => strtoupper($request->philHealthMember),
      'client_remarks' => $request->clientRemarks,
      'status' => 1,
    ];
    hr_members::where('id', $request->id)
      ->update($members);

    $contact = [
      'link_id' => $request->id,
      'street' => $request->street,
      'city' => $request->city,
      'province' => $request->province,
      'zip_code' => $request->zipCode,
      'email' => $request->email,
      'mobile_no' => $request->mobile,
    ];
    hr_contact::where('link_id', $request->id)
      ->update($contact);

    $philHealth = [
      'link_id' => $request->id,
      'philhealth_conditions' => $request->philHealthConditions,
      'position' => $request->position,
      'plan_type' => $request->planType,
      'branch_name' => $request->branchName,
      'philhealth_no' => $request->philHealthNo,
      'senior_citizen_id_no' => $request->seniorCitizenIDNo,
    ];
    hr_philhealth::where('link_id', $request->id)
      ->update($philHealth);
  }

  public function remove(Request $request)
  {
    $members = [
      'status' => 3,
    ];
    hr_members::where('id', $request->id)
      ->update($members);
  }

  public function forEnrollment(Request $request)
  {
    hr_members::where('id', $request->selected)
      ->update(['status' => 2]);
  }

  public function forCancellation(Request $request)
  {
    hr_members::whereIn('id', $request->selected)
      ->update(['status' => 8,]);
  }

  public function revokeCancellation(Request $request)
  {
    hr_members::whereIn('id', $request->selected)
      ->update(['status' => 4]);
  }

  public function forCorrection(Request $request)
  {
    $exists = hr_members_correction::where('link_id', $request->id)
      ->where('status', 1)
      ->exists();

    if ($exists) {

      $this->updateCorrection($request);
    } else {

      $this->createCorrection($request);

      hr_members::where('id', $request->id)
        ->update(['status' => 7]);
    }
  }

  public function createCorrection($request)
  {
    $members = [
      'link_id' => $request->id,
      'employee_no' => $request->empno,
      'last_name' => strtolower($request->lastName),
      'first_name' => strtolower($request->firstName),
      'middle_name' => strtolower($request->middleName),
      'extension' => strtoupper($request->extension),
      'gender' => strtoupper($request->gender),
      'member_type' => strtoupper($request->memberType),
      'birth_date' => $request->birthDate,
      'relationship_id' => $request->relationshipId,
      'civil_status' => $request->civilStatus,
      'effective_date' => $request->effectiveDate,
      'date_hired' => $request->dateHired,
      'reg_date' => $request->regDate,
      'if_enrollee_is_a_philhealth_member' => strtoupper($request->philHealthMember),
      'client_remarks' => $request->clientRemarks,
      'status' => 1,
    ];
    $member = hr_members_correction::create($members);

    $contact = [
      'link_id' => $member->id,
      'street' => $request->street,
      'city' => $request->city,
      'province' => $request->province,
      'zip_code' => $request->zipCode,
      'email' => $request->email,
      'mobile_no' => $request->mobile,
    ];
    hr_contact_correction::create($contact);

    $philHealth = [
      'link_id' => $member->id,
      'philhealth_conditions' => $request->philHealthConditions,
      'position' => $request->position,
      'plan_type' => $request->planType,
      'branch_name' => $request->branchName,
      'philhealth_no' => $request->philHealthNo,
      'senior_citizen_id_no' => $request->seniorCitizenIDNo,
    ];
    hr_philhealth_correction::create($philHealth);
  }

  public function updateCorrection($request)
  {
    $members = [
      'employee_no' => $request->empno,
      'last_name' => strtolower($request->lastName),
      'first_name' => strtolower($request->firstName),
      'middle_name' => strtolower($request->middleName),
      'extension' => strtoupper($request->extension),
      'gender' => strtoupper($request->gender),
      'member_type' => strtoupper($request->memberType),
      'birth_date' => $request->birthDate,
      'relationship_id' => $request->relationshipId,
      'civil_status' => $request->civilStatus,
      'effective_date' => $request->effectiveDate,
      'date_hired' => $request->dateHired,
      'reg_date' => $request->regDate,
      'if_enrollee_is_a_philhealth_member' => strtoupper($request->philHealthMember),
      'client_remarks' => $request->clientRemarks,
      'status' => 1,
    ];
    $member = hr_members_correction::where('link_id', $request->id)
      ->where('status', 1)
      ->update($members);

    $contact = [
      'link_id' => $request->link_id,
      'street' => $request->street,
      'city' => $request->city,
      'province' => $request->province,
      'zip_code' => $request->zipCode,
      'email' => $request->email,
      'mobile_no' => $request->mobile,
    ];
    hr_contact_correction::where('link_id', $request->link_id)
      ->update($contact);

    $philHealth = [
      'link_id' => $request->link_id,
      'philhealth_conditions' => $request->philHealthConditions,
      'position' => $request->position,
      'plan_type' => $request->planType,
      'branch_name' => $request->branchName,
      'philhealth_no' => $request->philHealthNo,
      'senior_citizen_id_no' => $request->seniorCitizenIDNo,
    ];
    hr_philhealth_correction::where('link_id', $request->link_id)
      ->update($philHealth);
  }

  public function revokeCorrection(Request $request)
  {
    hr_members::whereIn('id', $request->selected)
      ->update(['status' => 4]);
    hr_members_correction::whereIn('link_id', $request->selected)
      ->update(['status' => 3]);
  }

  public function getCorrection($id)
  {
    $list = DB::table('hr_members_correction as t1')
      ->join('hr_contact_correction as t2', 't1.id', '=', 't2.link_id')
      ->join('hr_philhealth_correction as t3', 't1.id', '=', 't3.link_id')
      ->select('t1.id', 't1.*', 't2.*', 't3.*')
      ->where('t1.link_id', $id)
      ->where('t1.status', 1)
      ->orderBy('t1.id', 'DESC')
      ->limit(1)
      ->get();

    return $list;
  }

  //Admin
  public function updateEnrollmentStatus(Request $request)
  {
    $members = [
      'certificate_no' => $request->certificateNo,
      'admin_remarks' => $request->adminRemarks,
      'changed_status_at' => date('Y-m-d H:i:s'),
      'status' => $request->changeStatus,
    ];
    hr_members::where('id', $request->id)
      ->update($members);
  }

  public function approveCancellation(Request $request)
  {
    $members = [
      'status' => 9,
    ];
    foreach ($request->selected as $key => $value) {
      hr_members::where('id', $value)
        ->update($members);
    }
  }

  public function exportEnrolled()
  {
    $status = request()->query('status');
    $data = hr_members::query()
      ->where('status', $status)
      ->join('hr_contact as hrcon', 'hr_members.id', 'hrcon.link_id',)
      ->join('hr_philhealth as hrphil', 'hr_members.id', 'hrphil.link_id',)
      ->select([
        'hr_members.employee_no',
        'hr_members.first_name',
        'hr_members.last_name',
        'hr_members.middle_name',
        'hr_members.extension',
        'hr_members.gender',
        'hrcon.street',
        'hrcon.city',
        'hrcon.province',
        'hrcon.zip_code',
        'hrcon.email',
        'hrcon.mobile_no',
        'hr_members.member_type',
        'hr_members.birth_date',
        'hr_members.relationship_id',
        'hr_members.civil_status',
        'hr_members.effective_date',
        'hr_members.date_hired',
        'hr_members.reg_date',
        'hr_members.if_enrollee_is_a_philhealth_member',
        'hrphil.philhealth_conditions',
        'hrphil.position',
        'hrphil.plan_type',
        'hrphil.branch_name',
        'hrphil.philhealth_no',
        'hrphil.senior_citizen_id_no',
        'hr_members.client_remarks',
        'hr_members.late_enrolled_remarks',
      ])
      ->get();
    return Excel::download(new LateEnrolledExport($data), 'late-enrolled-' . now()->format('Y-m-d') . '.xlsx');
  }

  public function uploadFile(Request $request)
  {
    $file = $request->file('file');
    $filename_hash = Str::uuid() . '.' . $file->getClientOriginalExtension();

    $file_uploaded_path = $file->storeAs('/public/uploaded-enrollee', $filename_hash);
    $file_path =  Storage::path($file_uploaded_path);

    $mailer = new SendingEmail(
      email: 'juniorarmando895@gmail.com',
      body: '<h1>hello</h1>',
      subject: 'TEST SUBJECT',
      attachments: [$file_path]
    );
    // $mailer->send();

    return $file_path;
  }

  public function retrieveFile()
  {
    // return Storage::url('uploaded-enrollee/1Fw94t8Wg1EvkzOo2rBKiulwf5OEs6VwPvdoH0Fb.txt', now()->addMinutes(5));
    // return Storage::disk('public')->exists('uploaded-enrollee/1Fw94t8Wg1EvkzOo2rBKiulwf5OEs6VwPvdoH0Fb.txt');
  }

  public function newEnrollment(NewEnrollmentRequest $request)
  {
    $member_id = $request->member_id;
    $birth_date = Carbon::parse($request->birthdate)->format('Y-m-d');
    $principalInfo = json_decode($request->principalInfo);

    if ($request->relation == 'PRINCIPAL') {
      $checkIfExistPrincipal = hr_members::query()->where(['member_id' => $member_id])->principal()->first();
      abort_if($checkIfExistPrincipal, 400, 'Principal already enrolled with the same employee number');
    } else {
      $checkIfExistMember = hr_members::query()->where('last_name', 'LIKE', '%' . $request->lastname . '%')->where('birth_date', $birth_date)->first();
      $checkingRelation = hr_members::query()->where('member_id', $member_id)->where('relationship_id', $request->relation)->count();

      abort_if($checkIfExistMember, 400, 'Dependent already enrolled');

      switch ($request->relation) {
        case 'SPOUSE':
        case 'DOMESTIC PARTNER':
          abort_if($checkingRelation >= 1, 400, 'Only 1 Spouse/Partner can be enrolled.');
          break;
        case 'PARENT':
          abort_if($checkingRelation >= 2, 400, 'Only 1 Parent can be enrolled.');
          break;
      }
    }



    DB::transaction(function () use ($request, $member_id, $birth_date, $principalInfo) {
      $enrollee = hr_members::create(
        [
          'client_company' => 'BROADPATH',
          'member_id' => $member_id,
          'hash' => $request->relation === 'PRINCIPAL' ? Str::uuid() : '',
          'relationship_id' => $request->relation,
          'first_name' => Str::upper($request->firstname),
          'last_name' => Str::upper($request->lastname),
          'middle_name' => Str::upper($request->middlename) ?? '',
          'birth_date' => $birth_date,
          'gender' => $request->gender,
          'civil_status' => $request->civilstatus,
          'date_hired' => Carbon::parse($request->hiredate)->format('Y-m-d'),
          'reg_date' => Carbon::parse($request->regularization_date)->format('Y-m-d'),
          'effective_date' => Carbon::parse($request->effectivity_date)->format('Y-m-d'),
          'coverage_date' => Carbon::now()->addYear(),
          'status' => 1,
          'created_by' => auth()->id(),
          'pending_submission_created_at' => Carbon::now(),
          'nationality' => $request->nationality,
        ]
      );

      $contact = hr_contact::create([
        'link_id' => $enrollee->id,
        'barangay' => $request->barangay,
        'street' => $request->street,
        'city' => $request->city,
        'province' => $request->province,
        'zip_code' => $request->zip_code,
        'email' => $request->email,
        'mobile_no' => $request->mobile_no,
      ]);

      $attachment = $request->has('attachment') ? $request->file('attachment') : [];
      foreach ($attachment as $key => $attch) {
        $path = $attch->store(env('APP_ENV') . '/members/attachments/' . $enrollee->id . '/' . $request->member_id, 'broadpath');
        $file_name = $attch->getClientOriginalName();

        HrMemberAttachment::create([
          'link_id' => $enrollee->id,
          'file_name' => $file_name,
          'file_link' => $path
        ]);

        hr_members::where('id', $enrollee->id)->update(['attachments' => ++$key]);
      }

      /**
       * CHANGE TO SOLO PARENT
       */
      if ($request->isMileStone && $request->relation == 'CHILD' && $principalInfo->principalCivilStatus == 'SINGLE') {
        hr_members::query()
          ->where('member_id', $principalInfo->principalMemberId)
          ->where('birth_date', $principalInfo->principalBirthDate)
          ->update(['civil_status' => 'SOLO PARENT']);

        hr_members::query()
          ->where('member_id', $principalInfo->principalMemberId)
          ->whereIn('relationship_id', ['SIBLING'])
          ->update(['status' => 7]);
      }

      /** 
       * CHANGE TO MARRIED
       */
      if ($request->isMileStone && $request->relation == 'SPOUSE' && $principalInfo->principalCivilStatus == 'SINGLE') {
        hr_members::query()
          ->where('member_id', $principalInfo->principalMemberId)
          ->where('birth_date', $principalInfo->principalBirthDate)
          ->update(['civil_status' => 'MARRIED']);

        hr_members::query()
          ->where('member_id', $principalInfo->principalMemberId)
          ->whereIn('relationship_id', ['PARENT', 'SIBLING'])
          ->update(['status' => 7]);
      }
    });

    return response()->json(['message' => 'Successfully save new enroll'], 201);
  }

  public function updateEnrollment(Request $request, $id)
  {
    $member_id = $request->member_id;
    $birth_date = Carbon::parse($request->birthdate)->format('Y-m-d');
    $attachment = $request->file('attachment');

    $enrollee = hr_members::find($id);
    $enrollee->member_id = $member_id;
    $enrollee->relationship_id = $request->relation;
    $enrollee->first_name = Str::upper($request->firstname);
    $enrollee->last_name = Str::upper($request->lastname);
    $enrollee->middle_name = Str::upper($request->middlename) ?? '';
    $enrollee->birth_date = $birth_date;
    $enrollee->gender = $request->gender;
    $enrollee->civil_status = $request->civilstatus;
    $enrollee->save();

    HrMemberAttachment::where('link_id', $id)->delete();
    foreach ($attachment as $key => $attch) {
      $path = $attch->store(env('APP_ENV') . '/members/attachments/' . $enrollee->id . '/' . $request->member_id, 'broadpath');
      $file_name = $attch->getClientOriginalName();

      HrMemberAttachment::create([
        'link_id' => $id,
        'file_name' => $file_name,
        'file_link' => $path
      ]);

      hr_members::where('id', $id)->update(['attachments' => ++$key]);
    }

    return response()->json(['message' => 'Successfully update enrollee', 'enrollee' => $enrollee]);
  }

  public function submitForEnrollment(Request $request)
  {
    $request->validate([
      'data' => 'required|array|min:1',
    ]);

    $ids = $request->data;

    $timestamp = Carbon::now()->timestamp;

    foreach ($ids as $key => $row) {
      $members = [
        'status' => 2,
        'excel_batch' => $timestamp,
      ];

      hr_members::where('id', $row)->update($members);
    }

    $filename = env('APP_ENV') . "/members/pending-for-submission/$timestamp.csv";
    $spacesFilename = env('DO_CDN_ENDPOINT') . '/' . $filename;
    $storingSuccess = Excel::store(new PendingForSubmissionExport(['id' => $ids]), $filename, 'broadpath');

    if (!$storingSuccess) {
      return response()->json(['message' => 'Uploading file failed.', $spacesFilename]);
    }

    $sending = new SendingEmail(
      email: 'glenilagan@llibi.com',
      body: view('send-pending-for-submission'),
      subject: 'PENDING FOR SUBMISSION',
      attachments: [$spacesFilename],
    );
    $sending->send();

    return response()->json(['message' => 'Submit for enrollment success.', $spacesFilename]);
  }

  public function deletePending(Request $request, $id)
  {
    return hr_members::where('id', $id)->where('status', 1)->delete();
  }

  public function submitForDeletion(Request $request)
  {
    $request->validate([
      'data' => 'required|array|min:1',
    ]);

    return $request->all();
  }

  public function excelTemplate()
  {
    return (new PhilcareMemberExport)->download('philcare-members-update.xlsx');
  }

  public function changePlan(Request $request, $id)
  {
    DB::transaction(function () use ($request, $id) {
      $member = hr_members::query()->where('id', $id)->first();
      $member->status = 8;
      $member->change_plan_at = Carbon::now();
      $member->client_remarks = $request->remarks;
      $member->save();

      HrMemberChangePlanCorrection::create([
        'member_link_id' => $id,
        'plan' => $request->plan,
        'created_by' => Auth::id(),
      ]);
    });

    return response()->json(['message' => 'Changing plan request success.']);
  }

  public function deleteMember(Request $request)
  {
    $member = hr_members::query()->where('id', $request->id)->first();

    DB::transaction(function () use ($request, $member) {
      // $member->deleted_remarks = $request->deleted_remarks;
      $member->status = 3;
      $member->pending_deleted_at = Carbon::parse($request->pending_deleted_at)->format('Y-m-d');
      $member->save();

      DeletionAttachment::create([
        'link_id' => $member->id,
        'file_name' => $request->file('death_document')->getClientOriginalName(),
        'file_link' => $request->file('death_document')->store(env('APP_ENV') . '/members/deletion/attachments/' . $member->id . '/' . $member->member_id, 'broadpath'),
      ]);
    });

    return response()->json(['message' => 'Success changing plan.', 'data' => $member]);
  }

  public function updateInformation(UpdateInformationRequest $request)
  {
    $member = hr_members::query()->where('id', $request->id)->firstOrFail();
    $member->pending_correction_at = Carbon::now();
    $member->status = StatusEnum::PENDING_CORRECTION->value;

    $data_for_save = [
      ...$request->validated(),
      'member_link_id' => $member->id,
      'pending_correction_at' => Carbon::now()
    ];

    $correction_member = hr_members_correction::create($data_for_save);
    $correction_contact = hr_contact_correction::create([
      'link_id' => $member->id,
      'email' => $request->email,
    ]);

    $member->save();

    return response()->json(['message' => 'Submit requesting for correction success.', 'data' => $correction_member]);
  }
}
