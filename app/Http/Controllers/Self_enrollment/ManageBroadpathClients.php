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

use App\Http\Controllers\Self_enrollment\ManageBroadpathNotifications;

use Carbon\Carbon;  // Carbon is used for date manipulation in Laravel

class ManageBroadpathClients extends Controller
{

    //CLIENT
    public function checkClient($id)
    {
        $principal = members::with(['contact:link_id,street,barangay,city,province,zip_code'])
            ->where('hash', $id)
            ->where('client_company', 'BROADPATH')
            ->where('relation', 'PRINCIPAL')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get(['id', 'member_id', 'last_name', 'first_name', 'middle_name', 'relation', 'birth_date', 'gender', 'civil_status', 'hire_date', 'coverage_date', 'mbl', 'room_and_board', 'status', 'form_locked']);

        $dependent = members::where('member_id', (count($principal) > 0 ? $principal[0]->member_id : 'xxxxx'))
            ->where('client_company', 'BROADPATH')
            ->where('relation', '!=', 'PRINCIPAL')
            ->whereIn('status', [2, 3, 4, 5])
            ->get(['id as mId', 'last_name', 'first_name', 'middle_name', 'relation', 'birth_date', 'gender', 'civil_status', 'attachments', 'status']);

        return array(
            'principal' => $principal,
            'dependent' => $dependent
        );
    }

    public function checkAgeByBirthdate($birthdate, $relationship)
    {
        // Convert the birthdate to a Carbon instance
        $dateOfBirth = Carbon::parse($birthdate);

        // Calculate the age
        $age = $dateOfBirth->age;

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
                case 1:

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
                        ->update(['form_locked' => 2, 'status' => 5]);

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
                case 2:
                    $member =
                        members::where('member_id', $request->member_id)
                        ->where('status', 4)
                        ->update(['status' => 2]);
                    break;
                case 3:
                    members::where('member_id', $request->member_id)
                        ->where('status', 4)
                        ->where('relation', '!=', 'PRINCIPAL')
                        ->update(['status' => 0]);

                    $member =
                        members::where('id', $request->id)
                        ->update(['status' => 1]);
                    break;
            }
        } else if (isset($request->changeAddress)) {
            $updateContact = [
                'street' => $this->clean($request->street),
                'barangay' => $this->clean($request->barangay),
                'city' => $this->clean($request->city),
                'province' => $this->clean($request->province),
                'zip_code' => $request->zipCode,
            ];
            $contact = contact::where('link_id', $request->linkId)
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
            $contact = contact::where('link_id', $request->id)
                ->update($updateContact);
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
            ->whereIn('status', [3, 5])
            ->get(['id', 'first_name', 'last_name', 'relation', 'birth_date']);

        $contact = contact::where('link_id', $principal[0]->id)
            ->get();

        $bill = 0;

        //premium checker
        switch ($principal[0]->mbl) {
            case 200000:
                $bill = 19807.2;
                break;
            case 150000:
                $bill = 19398.4;
                break;
        }

        $arr = [];
        $depInfo = '';
        $computation = '';
        $annual = 0;
        $monthly = 0;
        $succeeding = '';
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

            //lookup dependents order
            switch ($s) {
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

            $count = $i + 1;

            //breakdown of each dependents personal info
            $depInfo .= '<b>Dependent ' . $count . '</b>: ' . $this->clean($row->first_name) . ' ' . $this->clean($row->last_name) . ' -- ' . date('F j, Y', strtotime($row->birth_date)) . ' -- ' . strtoupper($row->relation) . '<br />';

            //if there is a 3rd and succeeding dependent, show this
            if ($i == 2)
                $succeeding =
                    '<br /><i style="font-size:14px;">By enrolling your 3rd and succeeding dependents, you are agreeing to 100% premium dependent contribution.</i><br />';

            if ($this->checkAgeByBirthdate($row->birth_date, $row->relation)) {
                //breakdown of each dependents premiusm computation
                $computation .=
                    '<tr>
                        <td colspan="2">' . $num . ' Dependent: ' . $bil . ' of ₱' . number_format($bill, 2) . ' = ' . number_format($com, 2) . '</td>
                    </tr>';

                //sum all dependents premium, that is their annual
                $annual += $com;

                $s++;
            } else {
                $computation .=
                    '<tr>
                        <td colspan="2">' . $num . ' Dependent: Overage. Removed from enrollment.</td>
                    </tr>';
            }

            $i++;
        }

        //divide annual for monthly
        $monthly = $annual / 52;

        //table for dependent premium computation
        $premiumComputation =
            '<table style="width:450px;border:2px solid black">
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
                    <td style="background-color:#fafafa;">' . number_format($annual, 2) . '</td>
                </tr>
                <tr>
                    <td style="background-color:#fafafa;font-weight:bold;">Weekly:</td>
                    <td style="background-color:#fafafa;">' . number_format($monthly, 2) . '</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:6px;"></td>
                </tr>
                    ' . $computation . '
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
            'name' => $principal[0]->last_name . ', ' . $principal[0]->first_name,
            'email'  => $contact[0]->email,
            'email2' => $contact[0]->email2,
            'mobile' => $contact[0]->mobile_no,
            'address' => $contact[0]->street . ', ' . $contact[0]->barangay . ', ' . $contact[0]->city . ', ' . $contact[0]->province . ', ' . $contact[0]->zip_code,
            'depInfo' => $depInfo,
            'succeeding' => $succeeding,
            'premiumComputation' => $premiumComputation
        ];

        (new ManageBroadpathNotifications)
            ->submittedWithDep($info);
    }

    public function submitDependent(Request $request)
    {
        //Check attachment first before continuing to processing the data
        $rules = [];
        $att = [];

        if (isset($request->list)) {
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
        }

        $update = [
            'civil_status' => $request->principalCivilStatus,
            'gender' => $request->genderPrincipal,
            'form_locked' => 2,
            'status' => 5
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

        $arr = [];
        $depInfo = '';
        $computation = '';
        $annual = 0;
        $monthly = 0;
        $succeeding = '';
        $premiumComputation = '';
        //breakdown of all dependents for enrollment
        if (isset($request->list)) {
            for ($i = 0; $i < count($request->list); $i++) {

                $arr['client_company'] = 'BROADPATH';
                $arr['member_id'] = $request->memberId;
                $arr['hire_date'] = $request->hireDate;
                $arr['coverage_date'] = $request->coverageDate;
                $arr['status'] = 5; //due to renewal changing the status to 5

                $arr['first_name'] = $this->clean($request->first_name[$i]);
                $arr['last_name'] = $this->clean($request->last_name[$i]);
                $arr['middle_name'] = $this->clean($request->middle_name[$i]);
                $arr['relation'] = strtoupper($request->relation[$i]);
                $arr['birth_date'] = $request->birth_date[$i];
                $arr['gender'] = strtoupper($request->gender[$i]);
                $arr['civil_status'] = strtoupper($request->civil_status[$i]);

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
                        $path = $file->storeAs('Self_enrollment/Broadpath/' . $request->memberId, $file->getClientOriginalName(), 'public');
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
                $depInfo .= '<b>Dependent ' . $count . '</b>: ' . $this->clean($request->first_name[$i]) . ' ' . $this->clean($request->last_name[$i]) . ' -- ' . date('F j, Y', strtotime($request->birth_date[$i])) . ' -- ' . strtoupper($request->relation[$i]) . '<br />';

                //if there is a 3rd and succeeding dependent, show this
                if ($i == 2)
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
                    <td colspan="2">' . $num . ' Dependent: ' . $bil . ' of ₱' . number_format($bill, 2) . ' = ' . number_format($com, 2) . '</td>
                </tr>';

                //sum all dependents premium, that is their annual
                $annual += $com;
            }

            //divide annual for monthly
            $monthly = $annual / 52;

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
                    <td style="background-color:#fafafa;">' . number_format($annual, 2) . '</td>
                </tr>
                <tr>
                    <td style="background-color:#fafafa;font-weight:bold;">Weekly:</td>
                    <td style="background-color:#fafafa;">' . number_format($monthly, 2) . '</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:6px;"></td>
                </tr>
                    ' . $computation . '
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
        }

        $info = [
            'name' => $upMember[0]->last_name . ', ' . $upMember[0]->first_name,
            'email'  => $upContact[0]->email,
            'email2' => $upContact[0]->email2,
            'mobile' => $upContact[0]->mobile_no,
            'address' => $upContact[0]->street . ', ' . $upContact[0]->barangay . ', ' . $upContact[0]->city . ', ' . $upContact[0]->province . ', ' . $upContact[0]->zip_code,
            'depInfo' => $depInfo,
            'succeeding' => $succeeding,
            'premiumComputation' => $premiumComputation
        ];

        (new ManageBroadpathNotifications)
            ->submittedWithDep($info);
    }

    public function submitWithoutDependent(Request $request)
    {
        //Check attachment first before continuing to processing the data
        $rules = [];
        $att = [];

        if (isset($request->list)) {
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
        }

        $update = [
            'civil_status' => $request->principalCivilStatus,
            'gender' => $request->genderPrincipal,
        ];
        $member = members::where('id', $request->principalId)
            ->update($update);

        $upMember = members::where('id', $request->principalId)
            ->where('status', 2)
            ->limit(1)
            ->get(['id', 'member_id', 'last_name', 'first_name', 'mbl']);

        $upContact = contact::where('link_id', $request->principalId)
            ->get();

        $bill = 0;

        $arr = [];
        $depInfo = '';
        $computation = '';
        $annual = 0;
        $monthly = 0;
        $succeeding = '';
        $premiumComputation = '';
        //breakdown of all dependents for enrollment
        if (isset($request->list)) {
            for ($i = 0; $i < count($request->list); $i++) {

                $arr['client_company'] = 'BROADPATH';
                $arr['member_id'] = $request->memberId;
                $arr['hire_date'] = $request->hireDate;
                $arr['coverage_date'] = $request->coverageDate;
                //$arr['status'] = 5; //due to renewal changing the status to 5

                $arr['first_name'] = $this->clean($request->first_name[$i]);
                $arr['last_name'] = $this->clean($request->last_name[$i]);
                $arr['middle_name'] = $this->clean($request->middle_name[$i]);
                $arr['relation'] = strtoupper($request->relation[$i]);
                $arr['birth_date'] = $request->birth_date[$i];
                $arr['gender'] = strtoupper($request->gender[$i]);
                $arr['civil_status'] = strtoupper($request->civil_status[$i]);
                $arr['status'] = 2;

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
                        $path = $file->storeAs('Self_enrollment/Broadpath/' . $request->memberId, $file->getClientOriginalName(), 'public');
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
        }
    }

    /* public function submitWithoutDependent(Request $request)
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
            'email'  => $upContact[0]->email,
            'email2' => $upContact[0]->email2,
            'mobile' => $upContact[0]->mobile_no,
        ];

        (new ManageBroadpathNotifications)
            ->submittedWithoutDep($info);
    }
    */

    public function clean($text)
    {
        return str_replace('ñ', 'Ñ', strtoupper($text));
    }

    //ADMIN
    function getNotificationBodyListOfApprovedClient($empno, $company)
    {

        $list = (new ManageSelfEnrollmentController)
            ->getSubmittedAndApprovedClients($empno, $company)['list'];

        $insSms = [];
        $insMail = [];
        foreach ($list as $key => $value) {

            $certNo = (!empty($value->certificate_no) ? $value->certificate_no : 'X');
            //if(!empty($value->certificate_no)) {
            $insSms[] =
                ($value->relation == "PRINCIPAL" ? "P-" : "D-") . ucwords(strtolower($value->last_name . ', ' . $value->first_name)) . ' / ' . $certNo;

            $insMail[] =
                '<tr>
                <td style="background-color:#fafafa;padding:4px;">' . ($value->relation == "PRINCIPAL" ? "Principal" : "Dependent") . '</td>
                <td style="background-color:#fafafa;padding:4px;">' . ucwords(strtolower($value->last_name)) . '</td>
                <td style="background-color:#fafafa;padding:4px;">' . ucwords(strtolower($value->first_name)) . '</td>
                <td style="background-color:#fafafa;padding:4px;">' . ucwords(strtolower($value->middle_name)) . '</td>
                <td style="background-color:#fafafa;padding:4px;">' . $certNo . '</td>
            </tr>';
            //}
        }

        return array(
            'sms' => $insSms,
            'mail' => $insMail
        );
    }

    public function getDetailsOfApprovedClient($id)
    {
        $client = DB::table('self_enrollment_members as t1')
            ->join('self_enrollment_contact as t2', 't1.id', '=', 't2.link_id')
            ->select('t1.first_name', 't1.last_name', 't2.email', 't2.email2', 't2.mobile_no')
            ->where('t1.relation', 'PRINCIPAL')
            ->where('t1.client_company', 'BROADPATH')
            ->where('t1.member_id', $id)
            ->orderBy('t1.member_id', 'ASC')
            ->get();

        $body = $this->getNotificationBodyListOfApprovedClient($id, 'BROADPATH');

        $info = [];
        foreach ($client as $key => $row) {
            $info = [
                'name' => $row->first_name . ' ' . $row->last_name,
                'email'  => $row->email,
                'email2' => $row->email2,
                'mobile' => $row->mobile_no,
                'insSms' => $body['sms'],
                'insMail' => $body['mail']
            ];
        }

        (new ManageBroadpathNotifications)
            ->approvedWithCertificateNo($info);
    }

    public function updateEnrollee($company, $request)
    {

        $list = (new ManageSelfEnrollmentController)
            ->getSubmittedAndApprovedClients($request->empno, $company)['list'];

        foreach ($list as $key => $value) {
            $members = [
                'certificate_no' => (!empty($request->{'certificateNo' . $value->id}) ? $request->{'certificateNo' . $value->id} : ''),
                'certificate_encode_datetime' => (!empty($request->{'certificateNo' . $value->id}) ? date('Y-m-d H:i:s') : ''),
                'status' => 4,
            ];
            members::where('id', $value->id)
                ->update($members);
        }

        $body = $this->getNotificationBodyListOfApprovedClient($request->empno, $company);

        $info = [
            'name' => $request->firstName . ' ' . $request->lastName,
            'email'  => $request->email,
            'email2' => $request->altEmail,
            'mobile' => $request->mobile,
            'insSms' => $body['sms'],
            'insMail' => $body['mail']
        ];

        (new ManageBroadpathNotifications)
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
                't2.mobile_no'
            )
            ->whereIn('t1.status', [1, 2, 4])
            //->whereIn('t1.member_id', ['LLIBI0027'])
            ->where('client_company', 'BROADPATH')
            ->where('t1.relation', 'PRINCIPAL')
            ->where('t1.form_locked', 1)
            ->orderBy('t1.id', 'DESC')
            ->get();

        $notificationTitle = 'No Reminders For Sending...';
        $notification = [];

        $app = 'SELF-ENROLLMENT';
        $clientCompany = 'BROADPATH';
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
                    'email'  => $row->email,
                    'email2' => $row->email2,
                    'mobile' => $row->mobile_no
                ];

                //check if there is notification set on that day
                if (!$exist) {

                    if ($dateFormLocked >= $checkdate && $dateFormLocked != $checkdate) {

                        if ($dateFinalWarning >= $checkdate && $dateFinalWarning != $checkdate) {

                            $status = 0;

                            //first day of enrollment
                            if ($checkdate == '2024-06-14') {
                                $notificationTitle = 'Reminder: Renewal Start';
                                $notification[] = [
                                    'Message' => 'Notification Sent',
                                    'to' => $info
                                ];

                                (new ManageBroadpathNotifications)
                                    ->rolloverInvite($info, $dateFinalWarning, $dateFormLocked);

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
                                    (new ManageBroadpathNotifications)
                                        ->rolloverEveryThreeDays($info, $dateFinalWarning, $dateFormLocked);

                                    //$status = 0;
                                    $status = 'UNTOUCHED FORM: EVERY THREE DAYS REMINDER';
                                }
                            }

                            //SEND WARNING FOR NO INTERACTION ON JUNE 18, 2024
                            if ($row->status == 4 && $checkdate == '2024-06-18') {
                                $notificationTitle = 'Reminder: No Interaction';
                                $notification[] = [
                                    'Message' => 'Notification Sent',
                                    'to' => $info
                                ];

                                (new ManageBroadpathNotifications)
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

                            (new ManageBroadpathNotifications)
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

                        (new ManageBroadpathNotifications)
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
}
