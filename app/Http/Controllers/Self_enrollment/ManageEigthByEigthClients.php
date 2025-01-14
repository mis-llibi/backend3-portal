<?php

namespace App\Http\Controllers\Self_enrollment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;

use App\Models\NotificationStatus;
use App\Models\Self_enrollment\members;
use App\Models\Self_enrollment\contact;
use App\Models\Self_enrollment\attachment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Self_enrollment\ManageEigthByEigthNotifications;
use App\Http\Controllers\Self_enrollment\ManageSelfEnrollmentController;

class ManageEigthByEigthClients extends Controller
{
    //CLIENT
    public function checkClient($id)
    {
        $principal = members::where('hash', $id)
            ->where('client_company', '8X8')
            ->where('relation', 'PRINCIPAL')
            ->select('id', 'member_id', 'last_name', 'first_name', 'middle_name', 'relation', 'birth_date', 'gender', 'civil_status', 'hire_date', 'coverage_date', 'mbl', 'room_and_board', 'milestone', 'form_locked', 'status')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();

        $dependent = [];
        if(count($principal) > 0)
            $dependent = members::where('member_id', $principal[0]->member_id)
                ->where('client_company', '8X8')
                ->where('relation', '!=', 'PRINCIPAL')
                ->where('status', 2)
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
            'status' => 2
        ];
        $member = members::where('id', $request->id)
            ->update($update);

        $updateContact = [
            'street' => $this->clean($request->street),
            'barangay' => $this->clean($request->barangay),
            'city' => $this->clean($request->city),
            'province' => $this->clean($request->province),
            'zip_code' => $request->zipCode,

        ];
        $contact = contact::where('link_id', $request->id)
                    ->update($updateContact);
    }

    public function submitDependent(Request $request)
    {
        //Check attachment first before continuing to processing the data
        $rules = []; $att = [];
        for ($i=0; $i < count($request->list); $i++) { 
            if($request->hasfile("attachment$i"))
            {   
                foreach($request->file("attachment$i") as $key => $file)
                {
                    $rules["dependent_$i"."_with_attachment_$key"] = 'mimes:jpg,jpeg,bmp,png,gif,svg,pdf';
                    $att["dependent_$i"."_with_attachment_$key"] = $file;
                }
            }
        }

        $validator = Validator::make($att, $rules);

        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
                'message' => 'Attachment/s must be an image or pdf only!'
            ) , 400);
        }

        $update = [
            'civil_status' => $request->principalCivilStatus,
            'gender' => $request->genderPrincipal,
        ];
        $member = members::where('id', $request->principalId)
            ->update($update);
        
        //update first the dependent to remove the unlisted for enrollment
        $dependent = members::where('relation', '!=', 'PRINCIPAL')
            ->where('member_id', $request->memberId)
            ->where('status', 2)
            ->update(['status' => 3]);

        $upMember = members::where('id', $request->principalId)
            ->where('status', 2)
            ->limit(1)
            ->get(['id', 'member_id', 'last_name', 'first_name', 'mbl']);

        $upContact = contact::where('link_id', $request->principalId)
            ->get();

        $bill = 0;

        //premium checker
        switch ($upMember[0]->mbl) {
            case 200000:
                $bill = 19807.2;
                break;
            case 150000:
                $bill = 19398.4;
                break;
        }

        $arr = []; $depInfo = ''; $computation = ''; $annual = 0; $monthly = 0; $succeeding = '';
        //breakdown of all dependents for enrollment
        for ($i=0; $i < count($request->list); $i++) { 

            $arr['client_company'] = '8X8';
            $arr['member_id'] = $request->memberId;
            $arr['hire_date'] = $request->hireDate;
            $arr['coverage_date'] = $request->coverageDate;
            $arr['status'] = 2;

            $arr['first_name'] = $this->clean($request->first_name[$i]);
            $arr['last_name'] = $this->clean($request->last_name[$i]);
            $arr['middle_name'] = $this->clean($request->middle_name[$i]);
            $arr['relation'] = strtoupper($request->relation[$i]);
            $arr['birth_date'] = $request->birth_date[$i];
            $arr['gender'] = strtoupper($request->gender[$i]);
            $arr['civil_status'] = strtoupper($request->civil_status[$i]);
            
            //add or update dependent information
            if(isset($request->id[$i]) && (string)$request->id[$i] != 'undefined') {
                members::where('id', $request->id[$i])
                    ->update($arr);
                $id = $request->id[$i];
            } else {
                $member = members::create($arr);
                $id = $member->id;
            }
            
            //check every dependents if they have attachments
            if($request->hasfile("attachment$i"))
            {
                foreach($request->file("attachment$i") as $key => $file)
                {
                    $path = $file->storeAs('Self_enrollment/8x8/'.$request->memberId, $file->getClientOriginalName(), 'public');
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

            $count = $i + 1;

            //breakdown of each dependents personal info
            $depInfo .= '<b>Dependent '.$count.'</b>: '.$this->clean($request->first_name[$i]).' '.$this->clean($request->last_name[$i]).' -- '.date('F j, Y', strtotime($request->birth_date[$i])).' -- '.strtoupper($request->relation[$i]).'<br />';

            //if there is a 3rd and succeeding dependent, show this
            if($i == 2)
                $succeeding = '<br /><i style="font-size:14px;">By enrolling your 3rd and succeeding dependents, you are agreeing to 100% premium dependent contribution.</i><br />';

            //lookup dependents order
            switch ($i) {
                case 0:
                    $num = $i + 1 . 'st';
                    $bil = '20%';
                    $com = $bill * 0.2;
                    break;
                case 1:
                    $num = $i + 1 . 'nd';
                    $bil = '20%';
                    $com = $bill * 0.2;
                    break;
                case 2:
                    $num = $i + 1 . 'rd';
                    $bil = '100%';
                    $com = $bill * 1;
                    break;
                default:
                    $num = $i + 1 . 'th';
                    $bil = '100%';
                    $com = $bill * 1;
                    break;
            }

            //breakdown of each dependents premiusm computation
            $computation .= 
            '<tr>
                <td colspan="2">'.$num.' Dependent: '.$bil.' of ₱'.number_format($bill,2).' = '.number_format($com,2).'</td>
            </tr>';

            //sum all dependents premium, that is their annual
            $annual += $com;
        }
        
        //divide annual for monthly
        $monthly = $annual / 12;

        //table for dependent premium computation
        $premiumComputation = 
        '<table style="width:350px;border:2px solid black">
            <tr>
                <td colspan="2" style="font-weight:bold;">
                    Your premium contribution is estimated as follows:
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding:6px;"></td>
            </tr>
            <tr>
                <td style="background-color:#fafafa;font-weight:bold;">Annual:</td>
                <td style="background-color:#fafafa;">'.number_format($annual,2).'</td>
            </tr>
            <tr>
                <td style="background-color:#fafafa;font-weight:bold;">Monthly:</td>
                <td style="background-color:#fafafa;">'.number_format($monthly,2).'</td>
            </tr>
            <tr>
                <td colspan="2" style="padding:6px;"></td>
            </tr>
            '.$computation.'
            <tr>
                <td colspan="2" style="padding:6px;"></td>
            </tr>
            <tr>
                <td colspan="2" style="color:red;font-weight:bold;">
                    Premium refund is not allowed if membership is terminated /
                    deleted mid policy year.
                </td>
            </tr>
        </table>';

        $info = [
            'name' => $upMember[0]->last_name.', '.$upMember[0]->first_name,
            'email' => $upContact[0]->email,
            'email2' => $upContact[0]->email2,
            'mobile' => $upContact[0]->mobile_no,
            'depInfo' => $depInfo,
            'succeeding' => $succeeding,
            'premiumComputation' => $premiumComputation
        ];

        (new ManageEigthByEigthNotifications)
            ->submittedWithDep($info);
    }
    
    public function submitWithoutDependent(Request $request)
    {
        $update = [
            'civil_status' => $request->civilStatus,
            'gender' => $request->gender,
        ];
        $principal = members::where('id', $request->id)
            ->update($update);

        $dependent = members::where('relation', '!=', 'PRINCIPAL')
            ->where('member_id', $request->memberId)
            ->where('status', 2)
            ->update(['status' => 3]);

        $upMember = members::where('id', $request->id)
            ->where('status', 2)
            ->limit(1)
            ->get(['id', 'last_name', 'first_name']);

        $upContact = contact::where('link_id', $upMember[0]->id)
                        ->get();
        
        $name = $upMember[0]->last_name.', '.$upMember[0]->first_name; 
        
        $info = [
            'name' => $upMember[0]->last_name.', '.$upMember[0]->first_name,
            'email' => $upContact[0]->email,
            'email2' => $upContact[0]->email2,
            'mobile' => $upContact[0]->mobile_no,
        ];

        (new ManageEigthByEigthNotifications)
            ->submittedWithoutDep($info);
    }

    public function clean($text)
    {
        return str_replace('ñ', 'Ñ', strtoupper($text));
    }

    //ADMIN
    public function updateEnrollee($company, $request)
    {
        $list = (new ManageSelfEnrollmentController)
            ->getSubmittedAndApprovedClients($request->empno, $company)['list'];
        
        foreach ($list as $key => $value) {
            $members = [
                'certificate_no' => (!empty($request->{'certificateNo'.$value->id}) ? $request->{'certificateNo'.$value->id} : '' ),
                'certificate_encode_datetime' => (!empty($request->{'certificateNo'.$value->id}) ? date('Y-m-d H:i:s') : '' ),
                'status' => 4,
            ];
            members::where('id', $value->id)
                ->update($members);
        }

        $list = (new ManageSelfEnrollmentController)
            ->getSubmittedAndApprovedClients($request->empno, $company)['list'];

        $insSms = []; $insMail = [];
        foreach ($list as $key => $value) {
            if(!empty($value->certificate_no)) {
                $insSms[] = 
                ($value->relation == "PRINCIPAL" ? "P-" : "D-").ucwords(strtolower($value->last_name.', '.$value->first_name)).' / '.$value->certificate_no;

                $insMail[] = 
                '<tr>
                    <td style="background-color:#fafafa;padding:4px;">'.($value->relation == "PRINCIPAL" ? "Principal" : "Dependent").'</td>
                    <td style="background-color:#fafafa;padding:4px;">'.ucwords(strtolower($value->last_name)).'</td>
                    <td style="background-color:#fafafa;padding:4px;">'.ucwords(strtolower($value->first_name)).'</td>
                    <td style="background-color:#fafafa;padding:4px;">'.ucwords(strtolower($value->middle_name)).'</td>
                    <td style="background-color:#fafafa;padding:4px;">'.$value->certificate_no.'</td>
                </tr>';
            }
        }

        $info = [
            'name' => $request->firstName.' '.$request->lastName,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'insSms' => $insSms,
            'insMail' => $insMail
        ];

        (new ManageEigthByEigthNotifications)
            ->approvedWithCertificateNo($info);
    }

    //CHECKING OF NOTIFICATION
    public function checkReminders($checkdate, $dateFinalWarning, $dateFormLocked)
    {
        $list = DB::table('self_enrollment_members as t1')
            ->join('self_enrollment_contact as t2', 't1.id', '=', 't2.link_id')
            ->select(
                't1.id', 
                't1.last_name', 
                't1.first_name', 
                't1.hash', 
                't1.upload_date', 
                't1.status',
                't1.form_locked',
                't2.email', 
                't2.email2',
                't2.mobile_no')
            ->whereIn('t1.status', [1, 2])
            ->where('client_company', '8X8')
            ->where('t1.relation', 'PRINCIPAL')
            ->where('t1.form_locked', 0)
            ->orderBy('t1.id', 'DESC')
            ->get();

        $notificationTitle = 'No Reminders For Sending...';
        $notification = [];

        $app = 'SELF-ENROLLMENT'; $clientCompany = '8X8';
        //check if there is still enrollee needs to be reminded
        if(count($list) > 0) {

            foreach ($list as $key => $row) {

                $exist = NotificationStatus::where('app', $app)
                    ->where('client_id', $row->id)
                    ->where('client_company', $clientCompany)
                    ->whereIn('status', [
                        'UNTOUCHED FORM: EVERY THREE DAYS REMINDER',
                        'UNTOUCHED FORM: FINAL REMINDER',
                        'UNTOUCHED FORM: LOCKED REMINDER',
                    ])
                    ->where('date', $checkdate)
                ->exists();
                
                $info = [
                    'hash'   => $row->hash,
                    'name'   => $row->last_name.', '.$row->first_name,
                    'email'  => $row->email,
                    'email2' => $row->email2,
                    'mobile' => $row->mobile_no
                ];

                //check if there is notification set on that day
                if(!$exist) {

                    if($dateFormLocked >= $checkdate && $dateFormLocked != $checkdate) {

                        if($dateFinalWarning >= $checkdate && $dateFinalWarning != $checkdate) {

                            $status = 0;
                            //check if still not submitting their enrollment then send notification
                            if($row->status == 1) {

                                $addedDay = 
                                date('Y-m-d H:i:s', strtotime($checkdate));
                                
                                $referenceDate = date_create($row->upload_date);
                                $date = date_create($addedDay);

                                $countDays = $date->diff($referenceDate)->format('%a');
                                $modulo = $countDays % 3;

                                $showMessage = 
                                    $modulo == 0 ? 
                                        "Notification Sent" : 
                                        "Notification Not Sent";

                                $notificationTitle = 'Reminder: 3 Days Interval';
                                $notification[] = [
                                    'Day/s' => $countDays,
                                    'Set' => $modulo,
                                    'Message' => $showMessage,
                                    'to' => $info
                                ];

                                if($modulo == 0) {
                                    (new ManageEigthByEigthNotifications)
                                        ->everyThreeDays($info, $dateFinalWarning, $dateFormLocked);

                                    $status = 'UNTOUCHED FORM: EVERY THREE DAYS REMINDER';
                                }

                            }
                            
                        } else {

                            $notificationTitle = 'Reminder: Final Warning';
                            $notification[] = [
                                'Message' => 'Notification Sent',
                                'to' => $info
                            ];

                            (new ManageEigthByEigthNotifications)
                                ->warningLastDay($info, $dateFinalWarning, $dateFormLocked);

                            $status = 'UNTOUCHED FORM: FINAL REMINDER';
                        }

                    } else {

                        $notificationTitle = 'Reminder: Form Locked';
                        $notification[] = [
                            'Message' => 'Notification Sent',
                            'to' => $info
                        ];

                        (new ManageEigthByEigthNotifications)
                            ->reminderLock($info);

                        $status = 'UNTOUCHED FORM: LOCKED REMINDER';

                        //set form to locked
                        members::where('id', $row->id)
                            ->update(['form_locked' => 1]);
                    }

                    if($status != 0)
                        NotificationStatus::create([
                            'app' => $app,
                            'client_id' => $row->id,
                            'client_company' => $clientCompany,
                            'status' => $status,
                            'date' => $checkdate
                        ]);
                }
            }

        }
        
        dd([$notificationTitle => $notification]);
    }
}