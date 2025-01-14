<?php



namespace App\Http\Controllers\Self_service;



use App\Http\Controllers\Controller;

use App\Http\Controllers\NotificationController;



use Illuminate\Http\Request;



use App\Models\Self_service\Client;

use App\Models\Self_service\ClientRequest;

use App\Models\Self_service\Complaints;

use App\Models\Self_service\Sync;



use App\Models\Corporate\Hospitals;

use App\Models\Corporate\ProviderLink;

use App\Models\Corporate\Doctors;

use App\Models\Holiday;

use App\Models\Self_service\Attachment;

use App\Models\Self_service\ExpiredLogs;

use App\Services\ClientErrorLogService;

use App\Services\GetActiveEmailProvider;

use Illuminate\Support\Facades\DB;



use Illuminate\Support\Facades\Validator;



use App\Services\SendingEmail;



use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Log;

use App\Events\RealtimeNotificationEvent;



class ClientController extends Controller

{





  public function ValidateClient(Request $request)

{

    $result = true;

    $response = '';

    $client = '';

    $link = 'self-service/client/request?req=' . $request->toDo;

    $error = null;

    $expired = false;



    $currentDateTime = Carbon::now();

    $isWeekends = $currentDateTime->isWeekend();

    $isFriday = $currentDateTime->isFriday();



    $threePm = Carbon::today()->setHour(15)->setMinute(0)->setSecond(0);

    $isCutOff = $currentDateTime->greaterThan($threePm);



    $isHoliday = Holiday::where('holiday_date', $currentDateTime->format('Y-m-d'))->exists();



    try {

        switch ((int)$request->toDo) {



            // REQUEST FOR LOA

            case 1:

                $principal = $this->CheckClient($request, 'principal');



                $dependent = [];

                if ($request->minorDependent) {

                    $dependent = $this->CheckClient($request, 'dependent');

                }



                if ($principal['count'] == 0) {

                    $result = false;



                    if ($isCutOff || $isWeekends || $isHoliday) {

                        $response = 'Membership validation is not available as of this time. We will process your request on the next business day. Meanwhile you may contact our 24/7 Client Care Hotline for urgent assistance.';

                    } else {

                        $response = 'We are unable to validate your information. Please check your input and try again.';

                    }

                }



                if ($request->minorDependent && $dependent['count'] == 0) {

                    $result = false;



                    if ($isCutOff || $isWeekends || $isHoliday) {

                        $response = 'Membership validation is not available as of this time. We will process your request on the next business day. Meanwhile you may contact our 24/7 Client Care Hotline for urgent assistance.';

                    } else {

                        $response = 'We are unable to validate your information. Please check your input and try again.';

                    }

                }



                if ($result) {

                    $client = $this->InsertClientData($request, $principal, $dependent);

                    $clientRequest = $this->RequestForLoa($request, $client);

                    $link .= '&loatype=' . $request->typeLOA . '&refno=' . $client['reference_number'];

                } else {

                    $error = ClientErrorLogService::saveLog(collect([

                        'firstName' => $request->firstName,

                        'lastName' => $request->lastName,

                        'dob' => $request->principalType == 1 ? $request->dob : $request->dob2,

                        'memberID' => isset($request->memberID) ? $request->memberID : null,

                        'depDob' => $request->dependentType == 1 ? $request->depDob : $request->depDob2,

                        'depFirstName' => $request->depFirstName,

                        'depLastName' => $request->depLastName,

                        'depMemberID' => isset($request->depMemberID) ? $request->depMemberID : null,

                        'minorDependent' => $request->minorDependent,

                        'requestType' => $request->toDo,

                        'typeLOA' => $request->typeLOA,

                    ]));

                }

                break;



            case 2:

                $principal = $this->CheckClient($request, 'principal');



                if ($principal['count'] == 0) {

                    $result = false;



                    if ($isCutOff || $isWeekends || $isHoliday) {

                        $response = 'Membership validation is not available as of this time. We will process your request on the next business day. Meanwhile you may contact our 24/7 Client Care Hotline for urgent assistance.';

                    } else {

                        $response = 'We are unable to validate your information. Please check your input and try again.';

                    }

                }



                if ($result) {

                    if (isset($principal['client'][0])) {

                        $link = ('https://llibi.app/service-request?router=1&memberid=' . $principal['client'][0]['member_id'] . '&password=' . $principal['client'][0]['birth_date']);

                    } else {

                        $result = false;

                        $response = 'Unexpected error: Client data is missing.';

                    }

                } else {

                    $error = ClientErrorLogService::saveLog(collect([

                        'firstName' => $request->firstName,

                        'lastName' => $request->lastName,

                        'dob' => $request->principalType == 1 ? $request->dob : $request->dob2,

                        'memberID' => isset($request->memberID) ? $request->memberID : null,

                        'depDob' => $request->dependentType == 1 ? $request->depDob : $request->depDob2,

                        'depFirstName' => $request->depFirstName,

                        'depLastName' => $request->depLastName,

                        'depMemberID' => isset($request->depMemberID) ? $request->depMemberID : null,

                        'minorDependent' => $request->minorDependent,

                        'requestType' => $request->toDo,

                        'typeLOA' => $request->typeLOA,

                    ]));

                }



                break;

            case 3:

                $result = '';

                // Code...

                break;

            case 4:

                $result = '';

                // Code...

                break;

            case 5:

                $result = '';

                // Code...

                break;

        }



        // Check incepto of client if the date is still valid

        if ($principal && isset($principal['client'][0])) {

            $incepto = Carbon::parse($principal['client'][0]['incepto']);

            $now = Carbon::now();

            if ($now->greaterThan($incepto)) {

                // create expired logs

                ExpiredLogs::create([

                    'member_id' => $principal['client'][0]['member_id'],

                    'company_code' => $principal['client'][0]['company_code'],

                    'first_name' => $principal['client'][0]['first_name'],

                    'last_name' => $principal['client'][0]['last_name'],

                    'incepfrom' => $principal['client'][0]['incepfrom'],

                    'incepto' => $principal['client'][0]['incepto'],

                    'birth_date' => $principal['client'][0]['birth_date'],

                ]);

            }

        }



        // Check if incepto of client's dependent is still valid

        if ($dependent && isset($dependent['client'][0])) {

            $incepto = Carbon::parse($dependent['client'][0]['incepto']);

            $now = Carbon::now();

            if ($now->greaterThan($incepto)) {

                // create expired logs

                ExpiredLogs::create([

                    'member_id' => $dependent['client'][0]['member_id'],

                    'company_code' => $dependent['client'][0]['company_code'],

                    'first_name' => $dependent['client'][0]['first_name'],

                    'last_name' => $dependent['client'][0]['last_name'],

                    'incepfrom' => $dependent['client'][0]['incepfrom'],

                    'incepto' => $dependent['client'][0]['incepto'],

                    'birth_date' => $dependent['client'][0]['birth_date'],

                ]);

            }

        }



        return response()->json([

            'link' => $link,

            'client' => $client,

            'response' => $result,

            'message' => $response,

            'error_data' => $error,

            'expired' => $expired

        ]);

    } catch (\Illuminate\Database\QueryException $qe) {

      $errorCode = $qe->getCode();

      $errorMessage = $qe->getMessage();



      // Check if the error is from incorrect details

      if ($errorCode == '42000') {

        Log::error('Database Query Error: ' . $errorMessage);



        return response()->json([

          'message' => 'Database Query Error: ' . $qe->getMessage()

        ]);

      }else{

        return response()->json([

            'message' => 'Database Query Error: ' . $qe->getMessage()

        ]);

      }

    } catch (\Exception $e) {

        return response()->json([

            'message' => 'Error: ' . $e->getMessage()

        ]);

    }

}



public function CheckClient($request, $type)

{

    $client = Sync::where(function ($query) use ($request, $type) {

        if ($type == 'dependent') {

            if ((int)$request->dependentType == 1) {

                $query->whereRaw('UPPER(first_name) = ?', [strtoupper($request->depFirstName)]);

                $query->where('last_name', 'like', '%' . strtoupper($request->depLastName) . '%');

                $query->where('birth_date', date('Y-m-d', strtotime($request->depDob)));

                $query->where('relation', '<>', 'EMPLOYEE');

            } else {

                $query->where('member_id', strtoupper($request->depMemberID));

                $query->where('birth_date', $request->depDob2);

                $query->where('relation', '<>', 'EMPLOYEE');

            }

        } else {

            if ((int)$request->principalType == 1) {

                $query->whereRaw('UPPER(first_name) = ?', [strtoupper($request->firstName)]);

                $query->where('last_name', 'like', '%' . strtoupper($request->lastName) . '%');

                $query->where('birth_date', date('Y-m-d', strtotime($request->dob)));

                $query->where('relation', 'EMPLOYEE');

            } else {

                $query->where('member_id', strtoupper($request->memberID));

                $query->where('birth_date', $request->dob2);

                $query->where('relation', 'EMPLOYEE');

            }

        }

    })->get();





    return array('client' => $client, 'count' => count($client));

}





  public function InsertClientData($request, $principal, $dependent)

  {

    $principal = [

      'request_type' => $request->toDo,

      'reference_number' => strtotime("now"), //date('YmdHis'),

      'member_id' => $principal['client'][0]['member_id'],

      'first_name' => $principal['client'][0]['first_name'],

      'last_name' => $principal['client'][0]['last_name'],

      'dob' => $principal['client'][0]['birth_date'],

      'incepto' => $principal['client'][0]['incepto'],

      'status' => 1,

      'platform' => $request->platform

    ];



    if (!empty($dependent)) {

      $dependent = [

        'is_dependent' => $request->minorDependent,

        'dependent_member_id' => $dependent['client'][0]['member_id'],

        'dependent_first_name' => $dependent['client'][0]['first_name'],

        'dependent_last_name' => $dependent['client'][0]['last_name'],

        'dependent_dob' => $dependent['client'][0]['birth_date'],

      ];

    }

    $client = Client::create(array_merge($principal, $dependent));



    return $client;

  }



  public function RequestForLoa($request, $client)

  {

    $client = [

      'client_id' => $client['id'],

      'member_id' => $request->minorDependent ? $client['dependent_member_id'] : $client['member_id'],

      'loa_type' => $request->typeLOA,

    ];



    $request = ClientRequest::create($client);



    return $client;

  }



  public function GetRequest($refno)

  {

    $request = DB::table('app_portal_clients as t1')

      ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')

      ->select('t1.id', 't1.reference_number as refno', 't1.email as email', 't1.contact as contact', 't1.member_id as memberID', 't1.first_name as firstName', 't1.last_name as lastName', 't1.dob as dob', 't1.is_dependent as isDependent', 't1.dependent_member_id as depMemberID', 't1.dependent_first_name as depFirstName', 't1.dependent_last_name as depLastName', 't1.dependent_dob as depDob', 't1.status as status', 't2.loa_type as loaType', 't2.provider', 't2.doctor_id as doctorID', 't2.doctor_name as doctorName')

      ->where('t1.reference_number', $refno)

      ->get();



    return $request;

  }



  function clean($string)

  {

    $string = str_replace('-', '', $string); // Replaces all hypens with nothing.

    $string = str_replace('+63', '', $string); // Replaces all +63 with nothing.



    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

  }



  public function UpdateRequest(Request $request)

  {

    $contact = '';

    if (!empty($this->clean($request->contact)))

      $contact = '0' . $this->clean($request->contact);



    $updateClient = Client::where('reference_number', $request->refno)

      ->update([

        'email' => $request->email,

        'alt_email' => $request->altEmail,

        'contact' => $contact,

        'status' => 2,

        'created_at' => Carbon::now(),

        'provider_email2' => isset($request->providerEmail2) ? $request->providerEmail2 : null,

        'is_send_to_provider' => isset($request->sendLoaToProvider) ? $request->sendLoaToProvider : 0,

      ]);



    $client = $this->GetRequest($request->refno);

    if ($request->loaType == 'consultation') {

      $complaint = $this->CheckComplaint($request->complaint);



      $setRequest = [

        'complaint' => $complaint,

        'assessment_q1' => $request->assessment1,

        'assessment_q2' => $request->assessment2,

        'assessment_q3' => $request->assessment3,

      ];

    } else {

      //Check attachment first before continuing to processing the data

      $rules = [];

      $att = [];

      if ($request->hasfile("attachment")) {

        foreach ($request->file("attachment") as $key => $file) {

          $rules["lab_attachment_$key"] = 'mimes:jpg,jpeg,bmp,png,gif,svg,pdf';

          $att["lab_attachment_$key"] = $file;

        }

      } else {

        return response()->json(array(

          'success' => false,

          'errors' => 'No Attachment/s',

          'message' => 'No attachment found, please upload image or pdf files only!'

        ), 400);

      }



      $validator = Validator::make($att, $rules);



      if ($validator->fails()) {

        return response()->json(array(

          'success' => false,

          'errors' => $validator->getMessageBag()->toArray(),

          'message' => 'Attachment/s must be an image or pdf only!'

        ), 400);

      }



      //check every dependents if they have attachments

      if ($request->hasfile("attachment")) {

        foreach ($request->file("attachment") as $key => $file) {

          $path = $file->storeAs('Self-service/LAB/' . $request->refno, $file->getClientOriginalName(), 'public');

          $name = $file->getClientOriginalName();



          Attachment::create([

            'request_id' => $client[0]->id,

            'file_name' => $name,

            'file_link' => $path

          ]);

        }

      }



      /*

                $this->validate($request, [

                    'attachLab' => 'required|mimes:jpeg,bmp,png,gif,svg',

                ]);



                $path = request('attachLab')->storeAs('Self-service/Laboratory_attachment/'.date('Ymdhis'), request('attachLab')->getClientOriginalName(), 'public');



                $setRequest = [

                    'lab_attachment' => 'storage/'.$path,

                ];

            */

    }



    if (isset($request->provider) && $request->provider != 'undefined') {

      $provider = explode('--', $request->provider);



      $hospital = explode('||', $provider[0]);

      $setRequest['provider_id'] = $hospital[0];

      $setRequest['provider'] = $hospital[1];



      $doctor = explode('||', $provider[1]);

      $setRequest['doctor_id'] = $doctor[0];

      $setRequest['doctor_name'] = $doctor[1];

    }



    $updateRequest = ClientRequest::where('client_id', $client[0]->id)

      ->update($setRequest);



    $this->sendNotification($request->refno, $client[0]->firstName . ' ' . $client[0]->lastName, $request->email, $request->altEmail, $contact, $request->loaType);



    $data = [

      'message' => 'New claim request',

      'date_created' => Carbon::now()->diffForHumans(),

    ];



    event(new RealtimeNotificationEvent($data));

    return $client;

  }



  public function CheckComplaint($complaintArr)

  {

    $complaint = [];

    if (isset($complaintArr)) {

      foreach ($complaintArr as $key => $value) {



        $nValue = strtoupper($value['label']);

        //$this->CheckComplaint($value['label']);

        $check = Complaints::where('title', 'like', '%' . $nValue . '%')

          ->get();

        if (count($check) == 0) {

          Complaints::create(['title' => $nValue]);

        }

        $complaint[] = $nValue;

      }

      $complaint = implode(', ', $complaint);

    }

    return $complaint;

  }



  public function SearchHospital(Request $request)

  {

    $request = Hospitals::where('status', 1)

      ->where('name', 'like', '%' . $request->search . '%')

      ->orderBy('name', 'ASC')

      ->limit(100)

      ->get(['id', 'name', 'add1 as address', 'city', 'state', 'email1', 'email2']);



    return $request;

  }



  public function SearchDoctor(Request $request)

  {

    $docs = $this->Links($request->hospitalid);



    $request = Doctors::where('status', 1)

      ->whereIn('id', $docs)

      ->where(function ($query) use ($request) {

        $query->orWhere('first', 'like', '%' . $request->search . '%')

          ->orWhere('last', 'like', '%' . $request->search . '%')

          ->orWhere('specialization', 'like', '%' . $request->search . '%');

      })

      ->orderBy('last', 'ASC')

      ->orderBy('first', 'ASC')

      ->limit(100)

      ->get(['id', 'first', 'last', 'specialization']);



    return $request;

  }



  public function Links($hospitalid)

  {

    $request = ProviderLink::where('status', 1)

      ->where('hospital_id', $hospitalid)

      ->get(['doctors_id']);



    return $request;

  }



  private function sendNotification($ref, $name, $email, $altEmail, $contact, $loaType)

  {

    $name = ucwords(strtolower($name));

    // $request->loaType == 'consultation'

    if (!empty($email)) {

      $attachment = [];



      $within = $loaType == 'consultation' ? '15 - 30' : '30 - 45';

      $mailMsg =

        '<p style="font-weight:normal;">

                Hi <b>' . $name . ',</b><br /><br />

                You have successfully submitted your request for LOA.<br /><br />

                Our Client Care will respond to your request within ' . $within . ' minutes.<br /><br />

                Your reference number is <b>' . $ref . '</b><br /><br />

                <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>

            </p>';





      switch (GetActiveEmailProvider::getProvider()) {

        case 'infobip':

          $emailer = new SendingEmail(email: $email, body: $mailMsg, subject: 'CLIENT CARE PORTAL - NOTIFICATION');

          $emailer->send();



          if (!empty($altEmail)) {

            $emailer = new SendingEmail(email: $altEmail, body: $mailMsg, subject: 'CLIENT CARE PORTAL - NOTIFICATION');

            $emailer->send();

          }

          break;



        default:

          $body = array('body' => $mailMsg, 'attachment' => $attachment);

          $mail = (new NotificationController)->NewMail($name, $email, $body);



          if (!empty($altEmail)) {

            $altMail = (new NotificationController)->NewMail($name, $altEmail, $body);

          }

          break;

      }

    }



    if (!empty($contact)) {

      $sms =

        "From Lacson & Lacson:\n\nHi $name,\n\nYou have successfully submitted your request for LOA.\n\nOur Client Care will respond to your request within $within minutes.\n\nYour reference number is $ref\n\nThis is an auto-generated SMS. Doesn’t support replies and calls.";



      $sms = (new NotificationController)->SmsNotification($contact, $sms);

    }



    return true;

  }

}

