<?php

namespace App\Http\Controllers\Members;

use App\Models\Members\hr_members;
use App\Models\Members\hr_contact;
use App\Models\Members\hr_philhealth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Imports\Members\MasterlistImport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;

class ManageMemberController extends Controller
{
    public function getMembersForClient()
    {
        $members = DB::table('hr_members as t1')
            ->join('hr_contact as t2', 't1.id', '=', 't2.link_id')
            ->join('hr_philhealth as t3', 't1.id', '=', 't3.link_id')
            ->select('t1.id', 't1.*', 't2.*', 't3.*')
            ->where('t1.status', 1)
            ->orderBy('t1.id', 'DESC')
            ->get();

        $pending = hr_members::where('status', 1)->get();
        $for_enrollment = hr_members::where('status', 2)->get();
        $for_deletion = hr_members::where('status', 3)->get();

        //$pending = array_count_values(array_column($members, 'status'))[$pending];
        return array(
                'members' => $members, 
                'pending' => count($pending), 
                'for_enrollment' => count($for_enrollment),
                'for_deletion' => count($for_deletion),
            );
    }

    public function getMembersForAdmin()
    {
        $members = DB::table('hr_members as t1')
            ->join('hr_contact as t2', 't1.id', '=', 't2.link_id')
            ->join('hr_philhealth as t3', 't1.id', '=', 't3.link_id')
            ->select('t1.id', 't1.*', 't2.*', 't3.*')
            ->where('t1.status', 1)
            ->orderBy('t1.id', 'DESC')
            ->get();

        $pending = hr_members::where('status', 1)->get();
        $for_enrollment = hr_members::where('status', 2)->get();
        $for_deletion = hr_members::where('status', 3)->get();

        //$pending = array_count_values(array_column($members, 'status'))[$pending];
        return array(
                'members' => $members, 
                'pending' => count($pending), 
                'for_enrollment' => count($for_enrollment),
                'for_deletion' => count($for_deletion),
            );
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

    public function forEnrollment(Request $request)
    {
        $members = [
            'status' => 2,
        ];
        foreach($request->selected as $key => $value) {
            hr_members::where('id', $value)
                ->update($members);            
        }
    }
}
