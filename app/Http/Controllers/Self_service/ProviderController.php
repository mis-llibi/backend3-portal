<?php

namespace App\Http\Controllers\Self_service;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;

use Illuminate\Http\Request;

use App\Models\Self_service\Client;
use App\Models\Self_service\ClientRequest;
use App\Models\Self_service\Complaints;
use App\Models\Self_service\Sync;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Response;

class ProviderController extends Controller
{
    public function SearchRequest($search, $id)
    {
        $request = DB::table('app_portal_clients as t1')
            ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
            ->select('t1.id', 't1.reference_number as refno', 't1.email as email', 't1.contact as contact', 't1.member_id as memberID', 't1.first_name as firstName', 't1.last_name as lastName', 't1.dob as dob', 't1.is_dependent as isDependent', 't1.dependent_member_id as depMemberID', 't1.dependent_first_name as depFirstName', 't1.dependent_last_name as depLastName', 't1.dependent_dob as depDob', 't1.status as status', 't2.loa_number as loaNumber', 't2.approval_code as approvalCode', 't2.loa_attachment as loaAttachment', 't2.complaint as complaint', 't2.lab_attachment as labAttachment', 't1.created_at as createdAt', 't2.provider_id as providerID', 't2.provider as providerName', 't2.doctor_id as doctorID', 't2.doctor_name as doctorName')
            ->whereIn('t1.status', [2, 3, 4])
            ->where(function ($query) use ($search, $id) {
                $query->orWhere('t2.loa_number', strtoupper($search));
                $query->orWhere('t2.approval_code', strtoupper($search));
            })
            ->limit(20)
            ->get();

        return $request;
    }

    public function GetRequest($id)
    {
        $request = DB::table('app_portal_clients as t1')
            ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
            ->select('t1.id', 't1.reference_number as refno', 't1.email as email', 't1.contact as contact', 't1.member_id as memberID', 't1.first_name as firstName', 't1.last_name as lastName', 't1.dob as dob', 't1.is_dependent as isDependent', 't1.dependent_member_id as depMemberID', 't1.dependent_first_name as depFirstName', 't1.dependent_last_name as depLastName', 't1.dependent_dob as depDob', 't1.status as status', 't2.loa_number as loaNumber', 't2.approval_code as approvalCode', 't2.loa_attachment as loaAttachment', 't2.complaint as complaint', 't2.lab_attachment as labAttachment', 't1.created_at as createdAt', 't2.provider_id as providerID', 't2.provider as providerName', 't2.doctor_id as doctorID', 't2.doctor_name as doctorName')
            ->whereIn('t1.status', [2, 3, 4])
            ->where('t1.id', $id)
            ->get();

        return $request;
    }

    public function UpdateRequest(Request $request)
    {
        $oclient = $this->GetRequest($request->id);

        $patientName = $oclient[0]->firstName.' '.$oclient[0]->lastName;
        $diagnosis = (isset($request->diagnosis) ? $request->diagnosis : '' );

        if((int)$request->status == 1) {
            if((int)$request->sendEmail == 1) {
                //$providerEmail = (!empty($request->updatedEmail) ? $request->updatedEmail : $request->registeredEmail);
                $providerEmail = (!empty($request->updatedEmail) ? $request->updatedEmail : 'mc_cimperial@yahoo.com');

                $loaAttachment = str_replace('storage', 'public', $oclient[0]->loaAttachment);

                $this->sendNotificationToProvider($diagnosis, $patientName, $loaAttachment, $oclient[0]->refno, $request->provider, $providerEmail);

                //CAEEMAIL
                $caeName = 'CAE';
                $caeEmail = 'mc_cimperial@yahoo.com';

                $this->sendNotificationToCAE($diagnosis, $patientName, $loaAttachment, $oclient[0]->refno, $request->provider, $providerEmail, $request->docName, $request->specialization, $caeName, $caeEmail);
            }
        } 

        $this->sendNotificationToClient($diagnosis, $oclient[0]->refno, $patientName, $oclient[0]->email, $oclient[0]->contact);

        $updateClient = Client::where('id', $request->id)
            ->update(['status' => 5]);

        $updateRequest = ClientRequest::where('client_id', $request->id)
            ->update([
                'diagnosis' => $diagnosis,
                'provider_updated_email' => isset($request->updatedEmail) ? $request->updatedEmail : NULL
            ]);

        //LOA ATTACHMENT
        $loaPath = storage_path('app/public').str_replace('storage', '', $oclient[0]->loaAttachment);
        
        if (file_exists($loaPath)) {
            return Response::download($loaPath);
        }
    }
    
    private function sendNotificationToProvider($diagnosis, $patientName, $attachment, $refno, $name, $email)
    {
        $name = ucwords(strtolower($name));

        if(!empty($email)) {
            $attachment = [$attachment];
            $mailMsg = 
            '<p style="font-weight:normal;">
                Hi <b>'.$name.',</b><br /><br />
                A copy of the used loa was sent to LLIBI, this serves as a confirmation.<br />
                You may use the reference number below to track the status of your request.
                <br /><br />
                Reference Number: <b>'.$refno.'</b><br />
                Patient Name: <b>'.$patientName.'</b><br />
                '.(!empty($diagnosis) ? 'Diagnosis: <b>'.$diagnosis.'</b><br />' : '' ).'<br />
                Thank you!
            </p>';

            $body = array('body' => $mailMsg, 'attachment' => $attachment);

            $mail = (new NotificationController)->MailNotification($name, $email, $body);
        }

        return true;
    }
    
    private function sendNotificationToClient($diagnosis, $refno, $name, $email, $contact)
    {
        $name = ucwords(strtolower($name));

        if(!empty($email)) {
            $attachment = [];
            $mailMsg = 
            '<p style="font-weight:normal;">
                Hi <b>'.$name.',</b><br /><br />
                Your LOA has been downloaded by the provider & set as completed to the system
                <br /><br />
                Reference Number: <b>'.$refno.'</b><br />
                '.(!empty($diagnosis) ? 'Diagnosis: <b>'.$diagnosis.'</b><br />' : '' ).'<br />
                Thank you!
            </p>';

            $body = array('body' => $mailMsg, 'attachment' => $attachment);

            $mail = (new NotificationController)->MailNotification($name, $email, $body);
        }

        if(!empty($contact)) {
            $diagnosis = (!empty($diagnosis) ? "Diagnosis: $diagnosis\n" : "" );
            $sms = 
            "Hi $name, \n\nYour LOA has been downloaded by the provider & set as completed to the system \n\nRef #: $refno \n$diagnosis \nThank you!";
            
            $sms = (new NotificationController)->SmsNotification($contact, $sms);
        }

        return true;
    }

    private function sendNotificationToCAE($diagnosis, $patientName, $attachment, $refno, $providerName, $providerEmail, $docName, $specialization, $name, $email)
    {
        $providerName = ucwords(strtolower($providerName));

        if(!empty($email)) {
            $attachment = [$attachment];
            $mailMsg = 
            '<p style="font-weight:normal;">
                Hi <b>'.$name.',</b><br /><br />
                Provider sent you a request for billing, please see below their info for tracking.
                <br /><br />
                Reference Number: <b>'.$refno.'</b> <br />
                ---------------------------------------------------<br />
                Hospital/Clinic: <b>'.$providerName.'</b> <br />
                Doctor: <b>'.(isset($docName) ? $docName : 'N/A').'</b> <br />
                Specialization: <b>'.(isset($specialization) ? $specialization : 'N/A' ).'</b> <br />
                Provider Email: <b>'.$providerEmail.'</b> <br />
                ---------------------------------------------------<br />
                Patient Name: <b>'.$patientName.'</b> <br />
                '.(!empty($diagnosis) ? 'Diagnosis: <b>'.$diagnosis.'</b><br />' : '' ).'<br />
                Thank you!
            </p>';

            $body = array('body' => $mailMsg, 'attachment' => $attachment);

            $mail = (new NotificationController)->MailNotification($name, $email, $body);
        }

        return true;
    }
}
