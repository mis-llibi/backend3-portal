<?php

namespace App\Http\Controllers\Self_service_mobile;

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

use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function ValidateClient(Request $request)
    {
        $result = true; $response = ''; $client = '';
        $link = 'request?req='.$request->toDo;
        switch ((int)$request->toDo) {

            //REQUEST FOR LOA
            case 1:
                $principal = $this->CheckClient($request, 'principal');

                $dependent = [];
                if($request->minorDependent)
                    $dependent = $this->CheckClient($request, 'dependent');

                if($principal['count'] == 0) 
                {
                    $result = false;
                    $response = 'System cannot find your information to our database, please check your input and try again';
                }

                if($request->minorDependent)
                {
                    if($dependent['count'] == 0)
                    {
                        $result = false; 
                        $response = 'System cannot find your dependent information to our database, please check your input and try again';
                    }
                }

                if($result) 
                {
                    $client = $this->InsertClientData($request, $principal, $dependent);
                    $clientRequest = $this->RequestForLoa($request, $client);
                    $link .= '&loatype='.$request->typeLOA.'&refno='.$client['reference_number'];
                }

                break;

            case 2:
                $result = '';
                # code...
                break;
            case 3:
                $result = '';
                # code...
                break;
            case 4:
                $result = '';
                # code...
                break;
            case 5:
                $result = '';
                # code...
                break;
            
        }

        return response()->json([
            'link' => $link,
            'client' => $client,
            'response' => $result, 
            'message' => $response,
        ]);
    }

    public function CheckClient($request, $type)
    {
        $client = Sync::where('member_id', session('user')['member_id'])
            ->get();

        return array('client' => $client, 'count' => count($client));
    }

    public function InsertClientData($request, $principal, $dependent)
    {
        $principal = [
            'request_type' => $request->toDo,
            'reference_number' => rand(10000, 99999).date('YmdHis'),
            'member_id' => $principal['client'][0]['member_id'],
            'first_name' => $principal['client'][0]['first_name'],
            'last_name' => $principal['client'][0]['last_name'],
            'dob' => $principal['client'][0]['birth_date'],
            'status' => 1,
        ];

        if(!empty($dependent))
        {
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
            ->select('t1.id', 't1.reference_number as refno', 't1.email as email', 't1.contact as contact', 't1.member_id as memberID', 't1.first_name as firstName', 't1.last_name as lastName', 't1.dob as dob', 't1.is_dependent as isDependent', 't1.dependent_member_id as depMemberID', 't1.dependent_first_name as depFirstName', 't1.dependent_last_name as depLastName', 't1.dependent_dob as depDob', 't1.status as status')
            ->where('t1.reference_number', $refno)
            ->get();

        return $request;
    }

    public function UpdateRequest(Request $request)
    {
        $updateClient = Client::where('reference_number', $request->refno)
            ->update([
                'email' => $request->email,
                'contact' => $request->contact,
                'status' => 2
            ]);

        $client = $this->GetRequest($request->refno);
        if($request->loaType == 'consultation')
        {
            $complaint = $this->CheckComplaint($request->complaint);

            $setRequest = [
                'complaint' => $complaint,
                'assessment_q1' => $request->assessment1,
                'assessment_q2' => $request->assessment2,
                'assessment_q3' => $request->assessment3,
            ];
        }
        else
        {
            $this->validate($request, [
                'attachLab' => 'required|mimes:jpeg,bmp,png,gif,svg',
            ]);

            $path = request('attachLab')->storeAs('Portal/Laboratory_attachment/'.date('Ymdhis'), request('attachLab')->getClientOriginalName(), 'public');

            $setRequest = [
                'lab_attachment' => 'storage/'.$path,
            ];
        }

        if(isset($request->provider)) {
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

        $this->sendNotification($request->refno, $client[0]->firstName.' '.$client[0]->lastName, $request->email, $request->contact);

        return $client;
    }

    public function CheckComplaint($complaintArr)
    {
        $complaint = [];
        if(isset($complaintArr))
        {   
            foreach ($complaintArr as $key => $value) {

                $nValue = strtoupper($value['label']);
                //$this->CheckComplaint($value['label']);
                $check = Complaints::where('title', 'like', '%'.$nValue.'%')
                    ->get();
                if(count($check) == 0)
                {
                    Complaints::create(['title' => $nValue]);
                }
                $complaint[] = $nValue;
            }
            $complaint = implode(', ', $complaint);
        }
        return $complaint;
    }

    private function sendNotification($ref, $name, $email, $contact)
    {
        $name = ucwords(strtolower($name));
        if(!empty($email)) {
            $attachment = [];
            $mailMsg = 
            '<p style="font-weight:normal;">
                Hi <b>'.$name.',</b><br /><br />
                You have successfully submitted your request for LOA, your reference number is '.$ref.'
                <br /><br />
                Thank you!
            </p>';

            $body = array('body' => $mailMsg, 'attachment' => $attachment);
            $mail = (new NotificationController)->MailNotification($name, $email, $body);
        }

        if(!empty($contact))
        {
            $sms = 
            "Hi $name \n\nYou have successfully submitted your request for LOA, your reference number is $ref, please use this as your tracking to your request. \n\n Thank you!";
            
            $sms = (new NotificationController)->SmsNotification($contact, $sms);
        }

        return true;
    }

    public function SearchHospital(Request $request)
    {
        $request = Hospitals::where('status', 1)
            ->where('name', 'like', '%'.$request->search.'%')
            ->orderBy('name', 'ASC')
            ->limit(100)
            ->get(['id', 'name', 'add1 as address', 'city', 'state', 'email1']);

        return $request;
    }

    public function SearchDoctor(Request $request)
    {
        $docs = $this->Links($request->hospitalid);

        $request = Doctors::where('status', 1)
            ->whereIn('id', $docs)
            ->where(function ($query) use($request) {
                $query->orWhere('first', 'like', '%'.$request->search.'%')
                      ->orWhere('last', 'like', '%'.$request->search.'%')
                      ->orWhere('specialization', 'like', '%'.$request->search.'%');
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
}
