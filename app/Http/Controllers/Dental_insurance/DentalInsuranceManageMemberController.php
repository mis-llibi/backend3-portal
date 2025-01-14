<?php

namespace App\Http\Controllers\Dental_insurance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Dental_insurance\Members;
use App\Models\Dental_insurance\Addons;

class DentalInsuranceManageMemberController extends Controller
{
    public function viewMembers(Request $request)
    {
        $query = $request->query();
        $data = $request->session()->all()['member'];

        $principal = Members::where('member_id', $data['member_id'])
            ->where('relation', 'EMPLOYEE')
            ->get();

        $dependent = Members::where('member_id', $data['member_id'])
            ->where('relation', '!=', 'EMPLOYEE')
            ->get();

        return ['principal' => $principal, 'dependent' => $dependent];
        //echo $query['memberId'];
        # code...
    }

    public function create(Request $request)
    {
        $member = [
            'member_id' => $request->memberId,
            'first_name' => strtoupper($request->firstName),
            'last_name' => strtoupper($request->lastName),
            'middle_name' => strtoupper($request->middleName),
            'birth_date' => $request->birthDate,
            'per_surface_light_cure' => $request->per_surface_light_cure,
            'per_tooth_light_cure' => $request->per_tooth_light_cure,
            'addition_oral_prophylaxis' => $request->addition_oral_prophylaxis,
            'peri_apical_x_ray' => $request->peri_apical_x_ray,
            'panoramic_dental_x_ray' => $request->panoramic_dental_x_ray,
            'surgical_tooth_extraction' => $request->surgical_tooth_extraction,
            'root_canal' => $request->root_canal,
            'relation' => 'DEP',
        ];
        $create = Members::create($member);
    }

    public function update(Request $request)
    {
        $depOld = $request->departmentOld;
        if(empty($depOld) && $request->relation == 'EMPLOYEE')
            $request->validate([
                'department' => ['required', 'string', 'max:255'],
            ]);

        $member = [
            'first_name' => strtoupper($request->firstName),
            'last_name' => strtoupper($request->lastName),
            'middle_name' => strtoupper($request->middleName),
            'birth_date' => strtoupper($request->birthDate),
            'department' => (!empty($request->department) ? $request->department : $depOld),
            'per_surface_light_cure' => (int)$request->per_surface_light_cure,
            'per_tooth_light_cure' => (int)$request->per_tooth_light_cure,
            'addition_oral_prophylaxis' => (int)$request->addition_oral_prophylaxis,
            'peri_apical_x_ray' => (int)$request->peri_apical_x_ray,
            'panoramic_dental_x_ray' => (int)$request->panoramic_dental_x_ray,
            'surgical_tooth_extraction' => (int)$request->surgical_tooth_extraction,
            'root_canal' => (int)$request->root_canal,
        ];
        $update = Members::where('id', (int)$request->id)
            ->update($member);
    }

    public function delete(Request $request)
    {
        $delete = Members::where('id', $request->id)
            ->delete();
    }
}
