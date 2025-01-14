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

use App\Http\Controllers\Self_enrollment\ManageDeelNotifications;

use Carbon\Carbon;  // Carbon is used for date manipulation in Laravel

class ManageDeelClients extends Controller
{
    //CLIENT
    public function checkClient($id)
    {
        $principal = members::with(['contact:link_id,street,barangay,city,province,zip_code'])
            ->where('hash', $id)
            ->where('client_company', 'DEEL')
            ->where('relation', 'PRINCIPAL')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get(['id', 'member_id', 'last_name', 'first_name', 'middle_name', 'relation', 'birth_date', 'gender', 'civil_status', 'hire_date', 'coverage_date', 'mbl', 'room_and_board', 'status', 'form_locked']);

        $dependent = members::where('member_id', (count($principal) > 0 ? $principal[0]->member_id : 'xxxxx'))
            ->where('client_company', 'DEEL')
            ->where('relation', '!=', 'PRINCIPAL')
            ->where('skip_hierarchy', '!=', 1)
            ->whereIn('status', [2, 3, 4, 5])
            ->get(['id as mId', 'last_name', 'first_name', 'middle_name', 'relation', 'birth_date', 'gender', 'civil_status', 'attachments', 'status', 'skip_hierarchy', 'skip_reason', 'skip_document']);

        return array(
            'principal' => $principal,
            'dependent' => $dependent
        );
    }

    public function checkAgeByBirthdate($birthdate, $relationship)
    {
        // Convert the birthdate to a Carbon instance
        $dob = Carbon::parse($birthdate);

        // Calculate the age
        $age = $dob->age;

        // Check the age based on the relationship type
        switch ($relationship) {
            case 'SIBLING':
            case 'CHILD':
                // Age must be >= 0 and <= 23
                return $age >= 0 && $age <= 23;
            case 'PARENT':
            case 'SPOUSE':
            case 'DOMESTIC PARTNER':
                // Age must be >= 18 and <= 65
                return $age >= 18 && $age <= 65;
            default:
                // If an unknown relationship type is passed, return false
                return false;
        }
    }

    public function checkIfExistingPrincipal($member_id, $birthDate)
    {
        $exist =
            members::where('member_id', $member_id)
            ->where('birth_date', $birthDate)
            ->exists();

        return $exist;
    }

    public function updateClientInfo(Request $request)
    {
        if (isset($request->rollover)) {
            switch ($request->rollover) {

                case 1: // rollover

                    $member =
                        members::where('member_id', $request->member_id)
                        ->where('status', 4)
                        ->where('relation', '!=', 'PRINCIPAL')
                        ->get(['id', 'relation', 'birth_date']);

                    $ids = [];
                    foreach ($member as $key => $row) {
                        if ($this->checkAgeByBirthdate($row->birth_date, $row->relation)) $ids[] = $row->id;
                    }

                    members::where('member_id', $request->member_id)
                        ->where('status', 4)
                        ->where('relation', 'PRINCIPAL')
                        ->update(['form_locked' => 3, 'status' => 5]);

                    members::where('member_id', $request->member_id)
                        ->where('status', 4)
                        ->where('relation', '!=', 'PRINCIPAL')
                        ->update(['status' => 3]);

                    members::where('member_id', $request->member_id)
                        ->where('status', 3)
                        ->where('relation', '!=', 'PRINCIPAL')
                        ->whereIn('id', $ids)
                        ->update(['status' => 5]);

                    $this->mailRollover($request->member_id);

                    break;
                case 2: //update existing

                    $member =
                        members::where('member_id', $request->member_id)
                        ->where('status', 4)
                        ->update(['status' => 2]);

                    break;
                case 3: //opt out

                    members::where('member_id', $request->member_id)
                        ->where('status', 4)
                        ->update(['status' => 0]);

                    $principal =
                        members::where('member_id', $request->member_id)
                        ->where('relation', 'PRINCIPAL')
                        ->get(['id', 'mbl', 'first_name', 'last_name']);

                    $contact = contact::where('link_id', $principal[0]->id)
                        ->get();

                    $info = [
                        'name' => $principal[0]->last_name . ', ' . $principal[0]->first_name,
                        'email'  => $contact[0]->email,
                        'email2' => $contact[0]->email2,
                        'mobile' => $contact[0]->mobile_no,
                    ];

                    (new ManageDeelNotifications)
                        ->submittedOptOut($info);

                    break;
                case 4:
                    $member =
                        members::where('member_id', $request->member_id)
                        ->where('status', 3)
                        ->update(['status' => 2]);
            }
        } else if (isset($request->changeAddress)) {
            $updateContact = [
                'street' => $this->clean($request->street),
                'barangay' => $this->clean($request->barangay),
                'city' => $this->clean($request->city),
                'province' => $this->clean($request->province),
                'zip_code' => $request->zipCode,
            ];
            contact::where('link_id', $request->linkId)
                ->update($updateContact);
        } else {
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
            contact::where('link_id', $request->id)
                ->update($updateContact);

            /* $update = [
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
                ->update($updateContact); */
        }
    }

    public function mailRollover($memberid)
    {

        $principal =
            members::where('member_id', $memberid)
            ->where('relation', 'PRINCIPAL')
            ->get(['id', 'mbl', 'first_name', 'last_name']);

        $member =
            members::where('member_id', $memberid)
            ->where('relation', '!=', 'PRINCIPAL')
            ->where('skip_hierarchy', '!=', 1)
            ->whereIn('status', [3, 5])
            ->get(['id', 'first_name', 'last_name', 'relation', 'birth_date']);

        $contact = contact::where('link_id', $principal[0]->id)
            ->get();

        $depInfo = '';
        $computation = '<tr><td></td></tr>';
        $premiumComputation = '';
        $i = 0;
        $s = 0;

        foreach ($member as $key => $row) {
            //lookup dependents order
            switch ($s) {
                case 0:
                    $num = $i + 1 . 'st';
                    break;
                case 1:
                    $num = $i + 1 . 'nd';
                    break;
                case 2:
                    $num = $i + 1 . 'rd';
                    break;
                default:
                    $num = $i + 1 . 'th';
                    break;
            }

            $count = $i + 1;

            //breakdown of each dependents personal info
            $depInfo .= '<b>Dependent ' . $count . '</b>: ' . $this->clean($row->first_name) . ' ' . $this->clean($row->last_name) . ' -- ' . date('F j, Y', strtotime($row->birth_date)) . ' -- ' . strtoupper($row->relation) . '<br />';


            if ($this->checkAgeByBirthdate($row->birth_date, $row->relation)) {
                //breakdown of each dependents premiusm computation
                $computation .=
                    '<tr>
                        <td>' . $num . ' Dependent: Retain</td>
                    </tr>';

                //sum all dependents premium, that is their annual

                $s++;
            } else {
                $computation .=
                    '<tr>
                        <td>' . $num . ' Dependent: Overage. Removed from enrollment.</td>
                    </tr>';
            }

            $i++;
        }

        //table for dependent premium computation
        $premiumComputation = '<table style="width:450px;">' . $computation . '</table>';

        $info = [
            'name' => $principal[0]->last_name . ', ' . $principal[0]->first_name,
            'email'  => $contact[0]->email,
            'email2' => $contact[0]->email2,
            'mobile' => $contact[0]->mobile_no,
            'address' => $contact[0]->street . ', ' . $contact[0]->barangay . ', ' . $contact[0]->city . ', ' . $contact[0]->province . ', ' . $contact[0]->zip_code,
            'depInfo' => $depInfo,
            'premiumComputation' => $premiumComputation
        ];

        (new ManageDeelNotifications)
            ->submittedWithDepRollover($info);
    }

    public function submitDependent(Request $request)
    {
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
            'status' => 5,
            'form_locked' => 2
        ];
        $member = members::where('id', $request->principalId)
            ->update($update);

        //update first the dependent to remove the unlisted for enrollment
        $dependent = members::where('relation', '!=', 'PRINCIPAL')
            ->where('member_id', $request->memberId)
            ->where('status', 2)
            ->update(['status' => 3]);

        $upMember = members::where('id', $request->principalId)
            ->where('status', 5)
            ->limit(1)
            ->get(['id', 'member_id', 'last_name', 'first_name', 'mbl']);

        $upContact = contact::where('link_id', $request->principalId)
            ->get();

        $arr = [];
        $depInfo = '';
        $depCount = 1;

        //breakdown of all dependents for enrollment
        for ($i = 0; $i < count($request->list); $i++) {

            $arr['client_company'] = 'DEEL';
            $arr['member_id'] = $request->memberId;
            $arr['hire_date'] = $request->hireDate;
            $arr['coverage_date'] = $request->coverageDate;
            $arr['status'] = 5;
            //$arr['form_locked'] = 2;

            $arr['first_name'] = $this->clean($request->first_name[$i]);
            $arr['last_name'] = $this->clean($request->last_name[$i]);
            $arr['middle_name'] = $this->clean($request->middle_name[$i]);
            $arr['relation'] = strtoupper($request->relation[$i]);
            $arr['birth_date'] = $request->birth_date[$i];
            $arr['gender'] = strtoupper($request->gender[$i]);
            $arr['civil_status'] = strtoupper($request->civil_status[$i]);

            //FOR SKIPPING
            if ($request->skip_hierarchy[$i]) {
                $arr['skip_hierarchy'] = 1;
                $arr['skip_reason'] = strtoupper($request->skip_reason[$i]);

                $skipFile = isset($request->skip_document[$i]) ? $request->skip_document[$i] : '';

                if (is_file($skipFile)) {
                    $skipDocPath = $skipFile->storeAs('Self_enrollment/Deel/Skip_docs/' . $request->memberId, $skipFile->getClientOriginalName(), 'public');
                    $arr['skip_document'] = $skipDocPath;
                }
            } else {
                $arr['skip_hierarchy'] = 0;
                $arr['skip_reason'] = '';
                $arr['skip_document'] = '';
            }

            //add or update dependent information
            if (isset($request->id[$i]) && (string)$request->id[$i] != 'undefined') {
                members::where('id', $request->id[$i])
                    ->update($arr);
                $id = $request->id[$i];
            } else {
                $member = members::create($arr);
                $id = $member->id;
            }

            //check every dependents if they have attachments
            if ($request->hasfile("attachment$i")) {
                foreach ($request->file("attachment$i") as $key => $file) {
                    $path = $file->storeAs('Self_enrollment/Deel/' . $request->memberId, $file->getClientOriginalName(), 'public');
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

            //breakdown of each dependents personal info
            if ($request->skip_hierarchy[$i] == 0) {

                $depInfo .= '<b>Dependent ' . $depCount . '</b>: ' . $this->clean($request->first_name[$i]) . ' ' . $this->clean($request->last_name[$i]) . ' -- ' . date('F j, Y', strtotime($request->birth_date[$i])) . ' -- ' . strtoupper($request->relation[$i]) . '<br />';

                $depCount++;
            }
        }

        $info = [
            'name' => $upMember[0]->last_name . ', ' . $upMember[0]->first_name,
            'email' => $upContact[0]->email,
            'email2' => $upContact[0]->email2,
            'mobile' => $upContact[0]->mobile_no,
            'address' => $upContact[0]->street . ', ' . $upContact[0]->barangay . ', ' . $upContact[0]->city . ', ' . $upContact[0]->province . ', ' . $upContact[0]->zip_code,
            'depInfo' => $depInfo,
        ];

        (new ManageDeelNotifications)
            ->submittedWithDepUpdateExisting($info);
    }

    public function submitWithoutDependent(Request $request)
    {
        $update = [
            'civil_status' => $request->principalCivilStatus,
            'gender' => $request->genderPrincipal,
            'status' => 5,
            'form_locked' => 2
        ];
        members::where('id', $request->principalId)
            ->update($update);

        members::where('relation', '!=', 'PRINCIPAL')
            ->where('client_company', 'DEEL')
            ->where('member_id', $request->memberId)
            ->where('status', 2)
            ->update(['status' => 3]);

        $upMember = members::where('id', $request->principalId)
            ->limit(1)
            ->get(['id', 'last_name', 'first_name']);

        $upContact = contact::where('link_id', $upMember[0]->id)
            ->get();

        $name = $upMember[0]->last_name . ', ' . $upMember[0]->first_name;

        $info = [
            'name' => $name,
            'email' => $upContact[0]->email,
            'email2' => $upContact[0]->email2,
            'mobile' => $upContact[0]->mobile_no,
        ];

        (new ManageDeelNotifications)
            ->submittedWithoutDep($info);
    }

    public function clean($text)
    {
        return str_replace('ñ', 'Ñ', strtoupper($text));
    }

    //CHECKING OF NOTIFICATION
    public function checkReminders1($checkdate, $dateFinalWarning, $dateFormLocked)
    {
        $list = DB::table('self_enrollment_members as t1')
            ->join('self_enrollment_contact as t2', 't1.id', '=', 't2.link_id')
            ->select(
                't1.id',
                't1.is_renewal',
                't1.vendor',
                't1.last_name',
                't1.first_name',
                't1.hash',
                't1.upload_date',
                't1.status',
                't1.form_locked',
                't2.email',
                't2.email2',
                't2.mobile_no'
            )
            //->whereIn('t1.status', [1, 2, 4])
            ->whereIn('t1.status', [2])
            //->whereIn('t1.member_id', ['LLIBI002063', 'LLIBI002062'])
            ->where('client_company', 'DEEL')
            ->where('t1.relation', 'PRINCIPAL')
            //->where('t1.form_locked', 1)
            //->limit(1)
            //->orderBy('t1.id', 'DESC')
            ->get();

        $notificationTitle = 'No Reminders For Sending...';
        $notification = [];

        $app = 'SELF-ENROLLMENT';
        $clientCompany = 'DEEL';
        //check if there is still enrollee needs to be reminded
        if (count($list) > 0) {

            foreach ($list as $key => $row) {

                $exist = NotificationStatus::where('app', $app)
                    ->where('client_id', $row->id)
                    ->where('client_company', $clientCompany)
                    ->whereIn('status', [
                        'START RENEWAL: FIRST DAY ENROLLMENT',
                        'UNTOUCHED FORM: EVERY THREE DAYS REMINDER',
                        'UNTOUCHED FORM: WARNING NO INTERACTION',
                        'UNTOUCHED FORM: FINAL REMINDER',
                        'UNTOUCHED FORM: LOCKED REMINDER',
                    ])
                    ->where('date', $checkdate)
                    ->exists();

                $info = [
                    'hash'   => $row->hash,
                    'name'   => $row->last_name . ', ' . $row->first_name,
                    'email'  => 'markimperial@llibi.com', //$row->email,
                    'email2' => '', //$row->email2,
                    'mobile' => $row->mobile_no
                ];

                //check if there is notification set on that day
                if ($exist) {

                    if ($dateFormLocked >= $checkdate && $dateFormLocked != $checkdate) {

                        if ($dateFinalWarning >= $checkdate && $dateFinalWarning != $checkdate) {

                            $status = 0;

                            //first day of enrollment
                            if ($checkdate == '2024-09-16') {
                                $notificationTitle = 'Reminder: Renewal Start';
                                $notification[] = [
                                    'Message' => 'Notification Sent',
                                    'to' => $info
                                ];

                                if ($row->vendor == 'PHILCARE' && $row->is_renewal == 1) (new ManageDeelNotifications)
                                    ->rolloverInvitePhilCare($info, $dateFinalWarning, $dateFormLocked);

                                if ($row->vendor == 'MAXICARE' && $row->is_renewal == 1) (new ManageDeelNotifications)
                                    ->rolloverInviteMaxiCare($info, $dateFinalWarning, $dateFormLocked);

                                /* if ($row->vendor == 'PHILCARE' && $row->is_renewal == 0) (new ManageDeelNotifications)
                                    ->invitePhilcare($info, $dateFinalWarning, $dateFormLocked);

                                if ($row->vendor == 'MAXICARE' && $row->is_renewal == 0) (new ManageDeelNotifications)
                                    ->inviteMaxicare($info, $dateFinalWarning, $dateFormLocked); */


                                /* (new ManageDeelNotifications)
                                    ->inviteWarning($info, $dateFinalWarning, $dateFormLocked);*/

                                //$status = 0;

                                $status = 'START RENEWAL: FIRST DAY ENROLLMENT';
                            }

                            //check if still not submitting their enrollment then send notification
                            //if($row->status == 1 || $row->status == 2) {
                            if ($row->status == 2) {

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

                                if ($modulo == 0) {
                                    (new ManageDeelNotifications)
                                        ->rolloverEveryThreeDays($info, $dateFinalWarning, $dateFormLocked);

                                    //$status = 0;
                                    $status = 'UNTOUCHED FORM: EVERY THREE DAYS REMINDER';
                                }
                            }

                            //SEND WARNING FOR NO INTERACTION ON JUNE 18, 2024
                            if ($row->status == 4 && $checkdate == '2024-09-20') {
                                $notificationTitle = 'Reminder: No Interaction';
                                $notification[] = [
                                    'Message' => 'Notification Sent',
                                    'to' => $info
                                ];

                                (new ManageDeelNotifications)
                                    ->rolloverWarningUntouchedForm($info, $dateFinalWarning, $dateFormLocked);

                                //$status = 0;
                                $status = 'UNTOUCHED FORM: WARNING NO INTERACTION';
                            }
                        } else {

                            $notificationTitle = 'Reminder: Final Warning';
                            $notification[] = [
                                'Message' => 'Notification Sent',
                                'to' => $info
                            ];

                            (new ManageDeelNotifications)
                                ->rolloverWarningLastDay($info, $dateFinalWarning, $dateFormLocked);

                            //$status = 0;
                            $status = 'UNTOUCHED FORM: FINAL REMINDER';
                        }
                    } else {

                        $notificationTitle = 'Reminder: Form Locked';
                        $notification[] = [
                            'Message' => 'Notification Sent',
                            'to' => $info
                        ];

                        (new ManageDeelNotifications)
                            ->rolloverReminderLock($info);

                        //$status = 0;
                        $status = 'UNTOUCHED FORM: LOCKED REMINDER';

                        //set form to locked
                        members::where('id', $row->id)
                            ->update([
                                'status' => 5,
                                'form_locked' => 2,
                                'kyc_timestamp' => date('Y-m-d H:i:s')
                            ]);
                    }

                    if ($status != 0)
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

    public function checkReminders($checkdate, $dateFinalWarning, $dateFormLocked)
    {

        $arr = [
            '39868rk',
            'mxxjq7k',
            'meq9k45',
            'mjp528v',
            'm4qg96g',
            '3pzv2k6',
            '3pzrgzy',
            'mw4gnvx',
            '3rgyvwn',
            'm986vvd',
            '3kpxk5n',
            'm4qyxej',
            '3vzjz48',
            'meqy9dg',
            '3z9w95q'
        ];

        $list = DB::table('self_enrollment_members as t1')
            ->join('self_enrollment_contact as t2', 't1.id', '=', 't2.link_id')
            ->select(
                't1.id',
                't1.is_renewal',
                't1.vendor',
                't1.last_name',
                't1.first_name',
                't1.hash',
                't1.upload_date',
                't1.status',
                't1.form_locked',
                't2.email',
                't2.email2',
                't2.mobile_no'
            )
            //->whereIn('t1.status', [2])
            //->where('t1.is_renewal', 0)
            ->whereIn('t1.member_id', $arr)
            ->where('client_company', 'DEEL')
            ->where('t1.relation', 'PRINCIPAL')
            ->orderBy('t1.id', 'DESC')
            ->get();

        $notificationTitle = 'No Reminders For Sending...';
        $notification = [];

        $app = 'SELF-ENROLLMENT';
        $clientCompany = 'DEEL';
        //check if there is still enrollee needs to be reminded
        if (count($list) > 0) {

            foreach ($list as $key => $row) {

                $info = [
                    'hash'   => $row->hash,
                    'name'   => $row->last_name . ', ' . $row->first_name,
                    'email'  => $row->email,
                    //'email2' => $row->email2,
                ];

                $result = (new ManageDeelNotifications)->rolloverForce($info, $dateFinalWarning, $dateFormLocked);

                //$result = (new ManageDeelNotifications)->rolloverLastDayRenewal($info, $dateFinalWarning, $dateFormLocked);

                //$result = (new ManageDeelNotifications)->rolloverLastDayEnrollment($info, $dateFinalWarning, $dateFormLocked);
            }
        }

        dd([$notificationTitle => $result]);
    }

    public function checkRemindersxx($checkdate, $dateFinalWarning, $dateFormLocked)
    {
        $list = DB::table('self_enrollment_members as t1')
            ->join('self_enrollment_contact as t2', 't1.id', '=', 't2.link_id')
            ->select(
                't1.id',
                't1.is_renewal',
                't1.vendor',
                't1.last_name',
                't1.first_name',
                't1.hash',
                't1.upload_date',
                't1.status',
                't1.form_locked',
                't2.email',
                't2.email2',
                't2.mobile_no'
            )
            ->whereIn('t1.status', [1, 2, 4, 5])
            //->whereIn('t1.member_id', ['LLIBI002063'])
            ->where('client_company', 'DEEL')
            ->where('t1.relation', 'PRINCIPAL')
            ->orderBy('t1.id', 'DESC')
            ->get();

        $notificationTitle = 'No Reminders For Sending...';
        $notification = [];

        $app = 'SELF-ENROLLMENT';
        $clientCompany = 'DEEL';
        //check if there is still enrollee needs to be reminded
        if (count($list) > 0) {

            foreach ($list as $key => $row) {

                $exist = NotificationStatus::where('app', $app)
                    ->where('client_id', $row->id)
                    ->where('client_company', $clientCompany)
                    ->whereIn('status', [
                        'SEND INVITE APOLOGY: ERROR LINK',
                    ])
                    ->where('date', $checkdate)
                    ->exists();

                $info = [
                    'hash'   => $row->hash,
                    'name'   => $row->last_name . ', ' . $row->first_name,
                    'email'  => $row->email,
                    'email2' => $row->email2,
                    'mobile' => '0' . $row->mobile_no,
                    'vendor' => $row->vendor,
                    'is_renewal' => $row->is_renewal,
                ];

                if (!$exist) {

                    $notificationTitle = 'Apology: Error Link';
                    $notification[] = [
                        'Message' => 'Notification Sent',
                        'to' => $info
                    ];

                    //check if there is notification set on that day
                    (new ManageDeelNotifications)
                        ->inviteApology($info, $dateFinalWarning, $dateFormLocked);


                    //$status = 0;
                    $status = 'SEND INVITE APOLOGY: ERROR LINK';

                    if ($status != 0)
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

    public function checkRemindersx($checkdate, $dateFinalWarning, $dateFormLocked)
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
                't2.mobile_no'
            )
            ->whereIn('t1.status', [1, 2])
            ->where('t1.client_company', 'DEEL')
            ->where('t1.relation', 'PRINCIPAL')
            ->where('t1.form_locked', 0)
            ->orderBy('t1.id', 'DESC')
            ->get();

        $notificationTitle = 'No Reminders For Sending...';
        $notification = [];

        $app = 'SELF-ENROLLMENT';
        $clientCompany = 'DEEL';
        //check if there is still enrollee needs to be reminded
        if (count($list) > 0) {

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
                    'name'   => $row->last_name . ', ' . $row->first_name,
                    'email'  => $row->email,
                    'email2' => $row->email2,
                    'mobile' => $row->mobile_no
                ];

                //check if there is notification set on that day
                if (!$exist) {

                    if ($dateFormLocked >= $checkdate && $dateFormLocked != $checkdate) {

                        if ($dateFinalWarning >= $checkdate && $dateFinalWarning != $checkdate) {

                            $status = 0;
                            //check if still not submitting their enrollment then send notification
                            if ($row->status == 1) {

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

                                if ($modulo == 0) {
                                    (new ManageDeelNotifications)
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

                            (new ManageDeelNotifications)
                                ->warningLastDay($info, $dateFinalWarning, $dateFormLocked);

                            $status = 'UNTOUCHED FORM: FINAL REMINDER';
                        }
                    } else {

                        $notificationTitle = 'Reminder: Form Locked';
                        $notification[] = [
                            'Message' => 'Notification Sent',
                            'to' => $info
                        ];

                        (new ManageDeelNotifications)
                            ->reminderLock($info);

                        $status = 'UNTOUCHED FORM: LOCKED REMINDER';

                        //set form to locked
                        members::where('id', $row->id)
                            ->update(['form_locked' => 1]);
                    }

                    if ($status != 0)
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
