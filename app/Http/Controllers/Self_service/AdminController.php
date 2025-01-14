<?php

namespace App\Http\Controllers\Self_service;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;

use Illuminate\Http\Request;

use App\Models\Self_service\Client;
use App\Models\Self_service\ClientRequest;
use App\Models\Self_service\Complaints;
use App\Models\Self_service\Sync;
use App\Models\Self_service\SyncCompanies;
use App\Models\Self_service\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use mikehaertl\pdftk\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SelfService\AdminExport;
use App\Models\Corporate\Hospitals;
use App\Services\GetActiveEmailProvider;
use App\Services\SendingEmail;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function SearchRequest($search, $id)
{
    // Define default statuses
    $defaultStatuses = [2, 6];

    $request = DB::table('app_portal_clients as t1')
        ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
        ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 't1.member_id')
        ->select(
            't1.id',
            't1.reference_number as refno',
            't1.email as email',
            't1.alt_email as altEmail',
            't1.contact as contact',
            't1.member_id as memberID',
            't1.first_name as firstName',
            't1.last_name as lastName',
            't1.dob as dob',
            't1.is_dependent as isDependent',
            't1.dependent_member_id as depMemberID',
            't1.dependent_first_name as depFirstName',
            't1.dependent_last_name as depLastName',
            't1.dependent_dob as depDob',
            't1.remarks as remarks',
            't1.status as status',
            't1.opt_landline as opt_landline',
            't1.callback_remarks as callback_remarks',
            't1.landline as landline',
            't1.opt_contact as opt_contact',
            't2.loa_type as loaType',
            't2.loa_number as loaNumber',
            't2.approval_code as approvalCode',
            't2.loa_attachment as loaAttachment',
            't2.complaint as complaint',
            't2.lab_attachment as labAttachment',
            't2.assessment_q1 as ass1',
            't2.assessment_q2 as ass2',
            't2.assessment_q3 as ass3',
            't1.created_at as createdAt',
            't2.provider_id as providerID',
            't2.provider as providerName',
            't2.doctor_id as doctorID',
            't2.doctor_name as doctorName',
            't2.diagnosis as diagnosis',
            't1.approved_date',
            DB::raw('TIMESTAMPDIFF(MINUTE, t1.created_at, t1.approved_date) as elapse_minutes'),
            DB::raw('TIMESTAMPDIFF(HOUR, t1.created_at, t1.approved_date) as elapse_hours'),
            'mlist.company_name',
            'mlist.company_code',
            't1.provider_email2',
            't1.is_send_to_provider',
            't1.platform'
        )
        ->where(function ($query) use ($id, $defaultStatuses) {
            if ($id == 8) {
                $query->whereIn('t1.status', $defaultStatuses); // Default statuses
            } elseif(is_array($id)){
                $query->where('t1.id', $id['val']);
            }else {
                $query->where('t1.status', $id);
            }
        })
        ->where(function ($query) use ($search) {
            if ($search != 0) {
                $query->orWhere('t1.member_id', 'like', '%' . strtoupper($search) . '%');
                $query->orWhere('t1.first_name', 'like', '%' . strtoupper($search) . '%');
                $query->orWhere('t1.last_name', 'like', '%' . strtoupper($search) . '%');

                $query->orWhere('t1.dependent_member_id', 'like', '%' . strtoupper($search) . '%');
                $query->orWhere('t1.dependent_first_name', 'like', '%' . strtoupper($search) . '%');
                $query->orWhere('t1.dependent_last_name', 'like', '%' . strtoupper($search) . '%');
            }
        })
        ->whereDate('t1.created_at', now()->format('Y-m-d'))
        ->orderBy('t1.id', 'DESC')
        ->limit(20)
        ->get();

    return $request;
}





//   public function SearchRequest($search, $id){
//     $request = DB::table('app_portal_clients as t1')
//       ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
//       ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 't1.member_id')
//       ->select(
//         't1.id',
//         't1.reference_number as refno',
//         't1.email as email',
//         't1.alt_email as altEmail',
//         't1.contact as contact',
//         't1.member_id as memberID',
//         't1.first_name as firstName',
//         't1.last_name as lastName',
//         't1.dob as dob',
//         't1.is_dependent as isDependent',
//         't1.dependent_member_id as depMemberID',
//         't1.dependent_first_name as depFirstName',
//         't1.dependent_last_name as depLastName',
//         't1.dependent_dob as depDob',
//         't1.remarks as remarks',
//         't1.status as status',
//         't2.loa_type as loaType',
//         't2.loa_number as loaNumber',
//         't2.approval_code as approvalCode',
//         't2.loa_attachment as loaAttachment',
//         't2.complaint as complaint',
//         't2.lab_attachment as labAttachment',
//         't2.assessment_q1 as ass1',
//         't2.assessment_q2 as ass2',
//         't2.assessment_q3 as ass3',
//         't1.created_at as createdAt',
//         't2.provider_id as providerID',
//         't2.provider as providerName',
//         't2.doctor_id as doctorID',
//         't2.doctor_name as doctorName',
//         't2.diagnosis as diagnosis',
//         't1.approved_date',
//         DB::raw('TIMESTAMPDIFF(MINUTE, t1.created_at, t1.approved_date) as elapse_minutes'),
//         DB::raw('TIMESTAMPDIFF(HOUR, t1.created_at, t1.approved_date) as elapse_hours'),
//         'mlist.company_name',
//         'mlist.company_code',
//         't1.provider_email2',
//         't1.is_send_to_provider',
//         't1.platform'
//       )
//       ->whereIn('t1.status', [2, 3, 4, 5, 6])
//       ->where(function ($query) use ($search, $id) {
//         if ($search != 0) {
//           $query->orWhere('t1.member_id', 'like', '%' . strtoupper($search) . '%');
//           $query->orWhere('t1.first_name', 'like', '%' . strtoupper($search) . '%');
//           $query->orWhere('t1.last_name', 'like', '%' . strtoupper($search) . '%');

//           $query->orWhere('t1.dependent_member_id', 'like', '%' . strtoupper($search) . '%');
//           $query->orWhere('t1.dependent_first_name', 'like', '%' . strtoupper($search) . '%');
//           $query->orWhere('t1.dependent_last_name', 'like', '%' . strtoupper($search) . '%');
//         }
//         if (is_array($id)) {
//           $query->where('t1.id', $id['val']);
//         } else {
//           if ($id != 0) {
//             $query->where('t1.status', $id);
//           }
//         }
//       })
//       ->whereDate('t1.created_at', now()->format('Y-m-d'))
//       ->orderBy('t1.id', 'DESC')
//       // ->offset(0)
//       ->limit(20)
//       ->get();


//     return $request;
//   }

//   public function UpdateRequest(Request $request)
//   {
//     $user_id = request()->user()->id;
//     $client = $this->SearchRequest(0, ['val' => $request->id]);

//     $status = (int)$request->status;

//     $arr = [
//       'status' => $status,
//       'remarks' => (isset($request->disapproveRemarks) ? strtoupper($request->disapproveRemarks) : ''),
//       'user_id' => $user_id,
//       'approved_date' => $status === 3 ? Carbon::now() : null,
//     ];

//     $updateClient = Client::where('id', $request->id)
//       ->update($arr);

//     $update = [];
//     $loa = [];



//     $directory = 'Self-service/LOA/' . $client[0]->memberID;

//     if(!Storage::disk('llibiapp')->exists($directory)){
//       Storage::disk('llibiapp')->makeDirectory($directory);
//     }

//     if ((int)$request->status == 3) {
//       $title = strtoupper($request->loaNumber);
//       $this->validate($request, [
//         'attachLOA' => 'required|mimes:pdf',
//       ]);

//       //Define the file name and directory path
//       $member_id = $client[0]->memberID;
//       $current_date = Carbon::now()->format('Ymd');
//       $created_at = Carbon::parse($client[0]->createdAt)->format('Ymd_His');
//       $fileName = $member_id . '_' . $current_date . '_' . $created_at . '.pdf';
//       $path = $directory . '/' . $fileName;



//       $uploadedPath = request('attachLOA')->storeAs($directory, $fileName, 'llibiapp');
//       $fileLink = env('DO_LLIBI_CDN_ENDPOINT') . '/' . $path;



//       // $path = request('attachLOA')->storeAs('Self-service/LOA/' . $client[0]->memberID, request('attachLOA')->getClientOriginalName(), 'public');

//       $update = [
//         'loa_attachment' => $fileLink,
//         'loa_number' => strtoupper($request->loaNumber), // Ensure LOA Number is stored
//         'approval_code' => strtoupper($request->approvalCode)
//       ];

//       $updateRequest = ClientRequest::where('client_id', $request->id)
//         ->update($update);

//       if ($client[0]->isDependent == 1) {
//         $password = date('Ymd', strtotime($client[0]->depDob));
//       } else {
//         $password = date('Ymd', strtotime($client[0]->dob));
//       }

//       $encryptedPdfPath = $this->encryptPdf($path, $password);

//       $loa = ['encryptedLOA' => $encryptedPdfPath];
//     }

//     $client = $this->SearchRequest(0, ['val' => $request->id]);
//     $allClient = $this->SearchRequest(0, 2);

//     $hospital_emails = [];
//     // if ($request->hospital_email1 != 'null') {
//     // array_push($hospital_emails, $request->hospital_email1);
//     // array_push($hospital_emails, 'testllibi1@yopmail.com');
//     // }
//     // if ($request->hospital_email2 != 'null') {
//     // array_push($hospital_emails, $request->hospital_email2);
//     // array_push($hospital_emails, 'testllibi2@yopmail.com');
//     // }

//     //SendNotification
//     $dataSend = [
//       'refno' => $client[0]->refno,
//       'remarks' => $request->disapproveRemarks,
//       'status' => $status,
//       'hospital_email' => $hospital_emails,
//       'provider_email2' => $client[0]->provider_email2,
//       'is_send_to_provider' => $client[0]->is_send_to_provider,
//       'company_code' => $client[0]->company_code,
//       'member_id' => $client[0]->memberID,
//       'request_id' => $client[0]->id,
//       'email_format_type' => $request->email_format_type
//     ];

//    $this->sendNotification(array_merge($dataSend, $update, $loa), $client[0]->firstName . ' ' . $client[0]->lastName, $client[0]->email, $client[0]->altEmail, $client[0]->contact);

//     return array('client' => $client, 'all' => $allClient);
//   }

public function UpdateRequest(Request $request)
{
  $user_id = request()->user()->id;
  $client = $this->SearchRequest(0, ['val' => $request->id]);

  $status = (int)$request->status;

  $arr = [
    'status' => $status,
    'remarks' => (isset($request->disapproveRemarks) ? strtoupper($request->disapproveRemarks) : ''),
    'user_id' => $user_id,
    'approved_date' => $status === 3 ? Carbon::now() : null,
  ];

  $updateClient = Client::where('id', $request->id)
    ->update($arr);

  $update = [];
  $loa = [];
  if ((int)$request->status == 3) {
    $title = strtoupper($request->loaNumber);
    $this->validate($request, [
      'attachLOA' => 'required|mimes:pdf',
    ]);

    $directory = 'Self-service/LOA/' . $client[0]->memberID;

    // if(!Storage::disk('llibiapp')->exists($directory)){
    //   Storage::disk('llibiapp')->makeDirectory($directory);
    // }

    $path = $request->attachLOA->storeAs($directory, $request->attachLOA->getClientOriginalName(), 'llibiapp');

    request('attachLOA')->storeAs('Self-service/LOA/' . $client[0]->memberID, request('attachLOA')->getClientOriginalName(), 'public');

    $update = [
      'loa_attachment' => env('DO_LLIBI_CDN_ENDPOINT') . "/" . $path,
    //   'loa_attachment' => 'storage/' . $path,
      'loa_number' => strtoupper($request->loaNumber),
      'approval_code' => strtoupper($request->approvalCode)
    ];

    $updateRequest = ClientRequest::where('client_id', $request->id)
      ->update($update);

    if ($client[0]->isDependent == 1) {
      $password = date('Ymd', strtotime($client[0]->depDob));
    } else {
      $password = date('Ymd', strtotime($client[0]->dob));
    }

    $encryptedPdfPath = $this->encryptPdf($path, $password);

    $loa = ['encryptedLOA' => $encryptedPdfPath];
  }

  $client = $this->SearchRequest(0, ['val' => $request->id]);
  $allClient = $this->SearchRequest(0, 2);

  $hospital_emails = [];
  // if ($request->hospital_email1 != 'null') {
  // array_push($hospital_emails, $request->hospital_email1);
  // array_push($hospital_emails, 'testllibi1@yopmail.com');
  // }
  // if ($request->hospital_email2 != 'null') {
  // array_push($hospital_emails, $request->hospital_email2);
  // array_push($hospital_emails, 'testllibi2@yopmail.com');
  // }

  //SendNotification
  $dataSend = [
    'refno' => $client[0]->refno,
    'remarks' => $request->disapproveRemarks,
    'status' => $status,
    'hospital_email' => $hospital_emails,
    'provider_email2' => $client[0]->provider_email2,
    'is_send_to_provider' => $client[0]->is_send_to_provider,
    'company_code' => $client[0]->company_code,
    'member_id' => $client[0]->memberID,
    'request_id' => $client[0]->id,
    'email_format_type' => $request->email_format_type
  ];

  $this->sendNotification(array_merge($dataSend, $update, $loa), $client[0]->firstName . ' ' . $client[0]->lastName, $client[0]->email, $client[0]->altEmail, $client[0]->contact);

  return array('client' => $client, 'all' => $allClient);
}

  private function encryptPdf($path, $password)
  {
    $filePath = Storage::path('public/' . $path);

    /* if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $pdf = new Pdf($filePath, [
                'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
                // or on most Windows systems:
                // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
                'useExec' => true,  // May help on Windows systems if execution fails
            ]);
        } else {
            $pdf = new Pdf($filePath);
        }

        $userPassword = 'admin123456';

        $result = $pdf->allow('AllFeatures')
            ->setPassword($password)
            ->setUserPassword($userPassword)
            ->passwordEncryption(128)
            ->saveAs($filePath);

        if ($result === false) {
            $error = $pdf->getError();
        } */

    return $filePath;
  }

  function removePastValue($in, $before)
  {
    $pos = strpos($in, $before);
    return $pos !== FALSE
      ? substr($in, $pos + strlen($before), strlen($in))
      : "";
  }

  function clean($string)
  {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
  }

  private function sendNotification($data, $name, $email, $altEmail, $contact)
  {

    $name = ucwords(strtolower($name));
    $remarks = $data['remarks'];
    $ref = $data['refno'];
    // $provider_email2 = $data['provider_email2'];
    $provider_email2 = 'testllibi1@yopmail.com';
    $is_send_to_provider = $data['is_send_to_provider'];
    $company_code = $data['company_code'];
    $member_id = $data['member_id'];
    $request_id = $data['request_id'];

    $loanumber = (!empty($data['loa_number']) ? $data['loa_number'] : '');
    $approvalcode = (!empty($data['approval_code']) ? $data['approval_code'] : '');

    if (!empty($email)) {

      $attachment = [];
      if ($data['status'] == 3) {
        $attach = $data['encryptedLOA'];
        $attachment = [$attach];
      }

      //$numbers = $data['status'] === 3 ? "LOA #: <b>$loanumber</b> <br /> Approval Code: <b>$approvalcode</b>" : ''; <br /><br /> Password to LOA is requestor birth date: <b style="color:red;">YYYYMMDD i.e., 19500312</b>

      $homepage = env('FRONTEND_URL');
      $feedbackLink = '
        <div>
          We value your feedback: <a href="' . $homepage . '/feedback/?q=' . Str::random(64) . '&rid=' . $request_id . '&compcode=' . $company_code . '&memid=' . $member_id . '&reqstat=' . $data['status'] . '">
            Please click here
          </a>
        </div>
        <div>
          <a href="' . $homepage . '/feedback/?q=' . Str::random(64) . '&rid=' . $request_id . '&compcode=' . $company_code . '&memid=' . $member_id . '&reqstat=' . $data['status'] . '">
          <img src="' . env('APP_URL', 'https://portal.llibi.app') . '/storage/ccportal_1.jpg" alt="Feedback Icon" width="300">
          </a>
        </div>
      <br /><br />';

      if ($data['status'] === 3) {
        $statusRemarks = 'Your LOA request is <b>approved</b>. Please print a copy LOA and present to the accredited provider upon availment.';
        // switch ($data['email_format_type']) {
        //   case 'consultation':
        //     break;
        //   case 'laboratory':
        //     $statusRemarks = '
        //     <p>Your LOA request is <b>approved</b>. Please print a copy of LOA and present to the accredited provider upon availment with doctor’s laboratory referral.</p>
        //     <p>This is a pre-approved Outpatient Procedure LOA with approval code for guaranteed amount indicated. If the guaranteed amount is less than the actual laboratory cost or there are additional laboratory procedures as advised by the doctor, please contact our Client Care Hotline for re-approval.</p>';
        //     break;
        //   case '2n1-standalone':
        //     $statusRemarks = '
        //     <p>Please print a copy of LOA and present to the accredited provider upon availment.</p>
        //     <p>Consultation LOA is pre-approved. Outpatient Procedure LOA is subject for Client Care’s approval based on doctor’s laboratory referral and evaluation of the diagnosis.</p>';
        //     break;
        //   case 'pre-approved-laboratory':
        //     $statusRemarks = '
        //     <p>Please print a copy of LOA and present to the accredited provider upon availment with doctor’s laboratory referral.</p>
        //     <p>This is a pre-approved Outpatient Procedure LOA with approval code for guaranteed amount indicated. If the guaranteed amount is less than the actual laboratory cost or there are additional laboratory procedures as advised by the doctor, please contact our Client Care Hotline for re-approval.</p>';
        //     break;

        //   default:
        //     throw new Exception("Email format is not supported", 1);
        //     break;
        // }
      } else {
        $statusRemarks = 'Your LOA request is <b>disapproved</b> with remarks: ' . $remarks;
        $feedbackLink = '';
      }


      // $mailMsg =
      //   '<p style="font-weight:normal;">
      //           Hi <b>' . $name . ',</b><br /><br />
      //           ' . $statusRemarks . '<br /><br />
      //           For further inquiry and assistance, feel free to contact us through our 24/7 Client Care Hotline.<br /><br />
      //           Manila Line: (02) 8236-6492<br/>
      //           Mobile number for Calls Only:<br />
      //           0917-8055424<br />
      //           0917-8855424<br />
      //           0919-0749433<br />

      //           Email: clientcare@llibi.com<br /><br />

      //           Your reference number is <b>' . $ref . '</b>.<br />
      //           ' . $feedbackLink . '
      //           <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>
      //       </p>';

      $body = array(
        'body' => view('send-request-loa', [
          'name' => $name,
          'statusRemarks' => $statusRemarks,
          'ref' => $ref,
          'feedbackLink' => $feedbackLink,
        ]),
        'attachment' => $attachment
      );

      switch (GetActiveEmailProvider::getProvider()) {
        case 'infobip':
          $mail = (new NotificationController)->EncryptedPDFMailNotification($name, $email, $body);
          if (!empty($altEmail)) {
            $altMail = (new NotificationController)->EncryptedPDFMailNotification($name, $altEmail, $body);
          }
          break;

        default:
          $mail = (new NotificationController)->NewMail($name, $email, $body);
          if (!empty($altEmail)) {
            $altMail = (new NotificationController)->NewMail($name, $altEmail, $body);
          }
          break;
      }

      // if ($is_send_to_provider == 1 && !empty($provider_email2)) {
      // $emailer = new SendingEmail(email: $provider_email2, body: $mailMsg, subject: 'CLIENT CARE PORTAL - NOTIFICATION', attachments: $attachment);
      // $emailer->send();
      // $altMail = (new NotificationController)->EncryptedPDFMailNotification($name, $provider_email2, $body);
      // }
    }

    if (!empty($contact)) {
      if ($data['status'] === 3) {
        $sms =
          "From Lacson & Lacson:\n\nHi $name,\n\nYour LOA request is approved, Please print a copy LOA and present to the accredited provider upon availment.\n\nFor further inquiry and assistance, feel free to contact us through our 24/7 Client Care Hotline.\n\nYour reference number: $ref\n\nThis is an auto-generated SMS. Doesn’t support replies and calls.";
        //\nLOA #: $loanumber\nAPP #: $approvalcode \n\n Thank you
      } else {
        $sms =
          "From Lacson & Lacson:\n\nHi $name,\n\nYour LOA request is disapproved with remarks: $remarks\n\nFor further inquiry and assistance, feel free to contact us through our 24/7 Client Care Hotline.\n\nYour reference number is $ref\n\nThis is an auto-generated SMS. Doesn’t support replies and calls.";
      }
      $sms = (new NotificationController)->SmsNotification($contact, $sms);
    }
  }

  //FILE UPLOADS
  public function getFiles($id)
  {
    $attachment = Attachment::where('request_id', $id)
      ->select('id', 'file_name', 'file_link')
      ->get();

    $client_request = Client::query()->with('clientRequest:id,client_id,loa_type')->where('id', $id)->first();

    return ['attachment' => $attachment, 'client_request' => $client_request];
  }

  public function export(Request $request)
  {
    $request_status = $request->status;
    $request_search = $request->search;
    $request_from = $request->from;
    $request_to = $request->to;

    $records = $this->exportRecords($request_search, $request_status, $request_from, $request_to)->toArray();

    return Excel::download(new AdminExport($records), 'records' . now()->format('Y-m-d') . '.xlsx');
  }

  public function previewExport(Request $request)
  {
    $request_status = $request->status;
    $request_search = $request->search;
    $request_from = $request->from;
    $request_to = $request->to;

    $records = $this->exportRecords($request_search, $request_status, $request_from, $request_to);

    return response()->json(['status' => true, 'message' => 'Fetching success', 'data' => $records]);
  }

  public function exportRecords($search = 0, $status = 2, $from = null, $to = null)
  {
    $request = DB::table('app_portal_clients as t1')
      ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
      ->leftJoin('users as user', 'user.id', '=', 't1.user_id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 't1.member_id')
      ->select(
        't1.id',
        't1.reference_number as refno',
        't1.email as email',
        't1.alt_email as altEmail',
        't1.contact as contact',
        't1.member_id as memberID',
        't1.first_name as firstName',
        't1.last_name as lastName',
        't1.remarks as remarks',
        't1.status as status',
        't2.loa_type as loaType',
        't2.loa_number as loaNumber',
        't2.approval_code as approvalCode',
        't2.complaint as complaint',
        't1.created_at as createdAt',
        't1.approved_date',
        't1.handling_time as elapse_minutes',
        // DB::raw('TIMESTAMPDIFF(MINUTE, t1.created_at, t1.approved_date) as elapse_minutes'),
        // DB::raw('TIMESTAMPDIFF(HOUR, t1.created_at, t1.approved_date) as elapse_hours'),
        'user.first_name as approved_by_first_name',
        'user.last_name as approved_by_last_name',
        'user.user_level',
        'mlist.company_name',
        't1.platform'
      )
      ->whereIn('t1.status', [2, 3, 4, 5])
      ->where(function ($query) use ($search, $status) {
        if ($search != 0) {
          $query->orWhere('t1.member_id', 'like', '%' . strtoupper($search) . '%');
          $query->orWhere('t1.first_name', 'like', '%' . strtoupper($search) . '%');
          $query->orWhere('t1.last_name', 'like', '%' . strtoupper($search) . '%');

          $query->orWhere('t1.dependent_member_id', 'like', '%' . strtoupper($search) . '%');
          $query->orWhere('t1.dependent_first_name', 'like', '%' . strtoupper($search) . '%');
          $query->orWhere('t1.dependent_last_name', 'like', '%' . strtoupper($search) . '%');
        }
        if (is_array($status)) {
          $query->where('t1.id', $status['val']);
        } else {
          if ($status != 0) {
            $query->where('t1.status', $status);
          }
        }
      });

    if ($from && $to) {
      $request = $request->whereDate('t1.created_at', '>=', $from)->whereDate('t1.created_at', '<=', $to);
    }

    $request = $request->orderBy('t1.id', 'DESC')->get();

    // foreach ($request as $key => $row) {
    //   $tat = 0;
    //   if ($row->approved_date) {
    //     $created_at = Carbon::parse($row->createdAt, 'Asia/Manila');
    //     $approved_at = Carbon::parse($row->approved_date, 'Asia/Manila');

    //     $valid_start = Carbon::parse('06:00'); // 6:00 AM
    //     $valid_end = Carbon::parse('18:00');   // 6:00 PM

    //     if ($created_at->isYesterday() && $approved_at->isToday()) {
    //       if ($created_at->format('H:i') > $valid_start->format('H:i') && $created_at->format('H:i') < $valid_end->format('H:i')) {
    //         $diff = Carbon::parse($created_at->format('H:i'))->diffInMinutes($valid_end);
    //         // Log::info('created ' . $diff);
    //         $tat += $diff;
    //       }

    //       // if ($approved_at->greaterThan($valid_start) && $approved_at->lessThan($valid_end)) {
    //       if ($approved_at->format('H:i') > $valid_start->format('H:i') && $approved_at->format('H:i') < $valid_end->format('H:i')) {
    //         $diff = Carbon::parse($valid_start)->diffInMinutes($approved_at->format('H:i'));
    //         // Log::info('approved ' . $diff);
    //         $tat += $diff;
    //       }
    //     }

    //     if ($created_at->isToday() && $approved_at->isToday()) {
    //       $diff = Carbon::parse($created_at)->diffInMinutes($approved_at);
    //       $tat += $diff;
    //     }
    //   }

    //   $request[$key]->elapse_minutes = $tat;
    //   $request[$key]->elapse_hours = $tat / 60;
    // }


    return $request;
  }

  public function viewBy(Request $request)
  {
    $user_id = request()->user()->id;

    $view_checker = Client::where('id', $request->id)->select('view_by')->first();

    if ($view_checker->view_by != NULL && ($view_checker->view_by != $user_id && $request->type == 'view')) {
      return response()->json(['status' => false, 'message' => 'This request is already handled by another CCE.']);
    }

    if ($request->type == 'view') {
      Client::where('id', $request->id)->update(['view_by' => $user_id]);
    } else {
      if ($view_checker->view_by == $user_id) {
        Client::where('id', $request->id)->update(['view_by' => null]);
      }
    }

    return response()->json(['status' => true, 'message' => 'Success.']);
  }

  private function emailIsValid($email)
  {
    $isValidEmail = preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email) === 1;
    return $isValidEmail;
  }

  function viewLogs(Request $request)
  {
    $result = DB::table('app_portal_clients as t1')
      ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 't1.member_id')
      ->join('users as user', 'user.id', '=', 't1.view_by')
      ->select(
        't1.id',
        't1.reference_number as refno',
        't1.email as email',
        't1.alt_email as altEmail',
        't1.contact as contact',
        't1.member_id as memberID',
        't1.first_name as firstName',
        't1.last_name as lastName',
        't1.remarks as remarks',
        't1.status as status',
        't2.loa_type as loaType',
        't2.loa_number as loaNumber',
        't2.approval_code as approvalCode',
        't2.complaint as complaint',
        't1.created_at as createdAt',
        't2.provider_id as providerID',
        't2.provider as providerName',
        't1.approved_date',
        DB::raw('TIMESTAMPDIFF(MINUTE, t1.created_at, t1.approved_date) as elapse_minutes'),
        DB::raw('TIMESTAMPDIFF(HOUR, t1.created_at, t1.approved_date) as elapse_hours'),
        'mlist.company_name',
        'mlist.company_code',
        't1.provider_email2',
        't1.is_send_to_provider',
        't1.view_by',
        'user.first_name as viewFirstname',
        'user.last_name as viewLastname',
        'user.email as viewEmail',
      )
      ->whereIn('t1.status', [2])
      ->orderBy('t1.id', 'DESC')
      ->limit(40)
      ->get();



    return $result;
  }

  public function renewImportCsv(Request $request)
  {
    $file = $request->file('file');

    $convertedToArray = $this->csvToArray($file->getRealPath());
    $data = [];
    for ($i = 0; $i < count($convertedToArray); $i++) {
      $birth_date = Carbon::createFromFormat('d/m/Y', $convertedToArray[$i]['birth_date']);
      $data[] = [
        'empid' => $convertedToArray[$i]['﻿employee_id'],
        'bday' => $birth_date,
        'name' => $convertedToArray[$i]['last_name'] . ', ' . $convertedToArray[$i]['first_name'],
      ];
    }

    // DB::connection('mysql_claims')->table('table_test')->insert($data);
    return 'Jobi done or what ever';
  }

  function csvToArray($filename = '', $delimiter = ',')
  {
    if (!file_exists($filename) || !is_readable($filename))
      return false;

    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
        if (!$header) {
          $header = $row;
        } else {
          $data[] = array_combine($header, $row);
        }
      }
      fclose($handle);
    }

    return $data;
  }

  public function pendingCounter()
  {
    $request = DB::table('app_portal_clients as t1')
      ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 't1.member_id')
      ->select('t1.id')
      ->where('t1.status', 2)
      ->limit(40)
      ->count();

    return ['pending' => $request];
  }

  public function updateTAT()
  {
    abort(418);
    $request = DB::table('app_portal_clients as t1')
      ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
      ->leftJoin('users as user', 'user.id', '=', 't1.user_id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 't1.member_id')
      ->select(
        't1.id',
        't1.created_at as createdAt',
        't1.approved_date',
        't1.handling_time as elapse_minutes',
      )
      ->whereNotNull('t1.approved_date')
      ->orderByDesc('t1.id')
      ->whereDate('t1.created_at', '>=', '2023-09-01')
      ->whereDate('t1.created_at', '<=', '2023-09-24')
      ->get();

    foreach ($request as $key => $row) {
      $elapse_minutes = Carbon::parse($row->createdAt)->diffInMinutes($row->approved_date);

      DB::table('app_portal_clients')->where('status', 3)
        ->where('id', $row->id)
        ->whereDate('created_at', '>=', '2023-09-01')
        ->whereDate('created_at', '<=', '2023-09-24')->update([
          'handling_time' => $elapse_minutes
        ]);
    }

    return [count($request), $request];
  }

  // Get Company from DB
  public function getCompanies()
  {
    // put an alias for corporate_compcode as code
    $company = SyncCompanies::select('name', 'corporate_compcode as code')->get();

    return $company;
  }

}
