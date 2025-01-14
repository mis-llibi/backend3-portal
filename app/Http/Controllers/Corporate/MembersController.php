<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Corporate\Employees;
use App\Models\Corporate\Dependents;
use App\Models\Corporate\CompaniesSync;

use Illuminate\Support\Facades\DB;

class MembersController extends Controller
{
    public function GetEbdCode($corporateCode)
    {
        $request = CompaniesSync::where('corporate_compcode', $corporateCode)
            ->limit(1)
            ->get(['ebd_compcode']);

        return (count($request) > 0 ? $request[0]->ebd_compcode : '');
    }

    public function GetEmployees($status, $lastName, $firstname)
    {
        if($status == 'current') {
            $list = DB::connection('mysql_corporate')
                    ->table('employees as t1')
                    ->join('companies as t2', 't1.company_id', '=', 't2.id')
                    ->where('t1.last', 'like', '%'.$lastName.'%')
                    ->where('t1.given', 'like', '%'.$firstname.'%')
                    ->orderBy('t1.id', 'DESC')
                    ->limit(1)
                    ->get([
                        't1.given as first_name',
                        't1.middle as middle_name',
                        't1.last as last_name',
                        't2.code as company_code',
                        't2.name as company_name',
                    ]);
        } else {
            $list = DB::connection('mysql_corporate')
                    ->table('employees_previous as t1')
                    ->join('companies as t2', 't1.company_id', '=', 't2.id')
                    ->where('t1.last', 'like', '%'.$lastName.'%')
                    ->where('t1.given', 'like', '%'.$firstname.'%')
                    ->orderBy('t1.id', 'DESC')
                    ->limit(1)
                    ->get([
                        't1.given as first_name',
                        't1.middle as middle_name',
                        't1.last as last_name',
                        't2.code as company_code',
                        't2.name as company_name',
                    ]);
        }
        
        $nlist = [];
        if(count($list) > 0) {
            foreach ($list as $key => $row) {
                $nlist[] = [
                    'first_name' => $row->first_name,
                    'middle_name' => $row->middle_name,
                    'last_name' => $row->last_name,
                    //'company_code' => $row->company_code,
                    //'company_name' => $row->company_name,
                    'ebd_comp_code' => $this->GetEbdCode($row->company_code)           
                ];
            }
        }

        return array('list' => $nlist);
    }

    public function GetDependents($status, $lastName, $firstname)
    {
        if($status == 'current') {
            $list = DB::connection('mysql_corporate')
                ->table('dependents as t1')
                ->join('employees as t2', 't1.employee_id', '=', 't2.id')
                ->join('companies as t3', 't2.company_id', '=', 't3.id')
                ->where('t1.last', 'like', '%'.$lastName.'%')
                ->where('t1.given', 'like', '%'.$firstname.'%')
                ->orderBy('t1.id', 'DESC')
                ->limit(1)
                ->get([
                    't1.given as first_name',
                    't1.middle as middle_name',
                    't1.last as last_name',
                    't3.code as company_code',
                    //'t3.name as company_name',
                ]);
        } else {
            $list = DB::connection('mysql_corporate')
                    ->table('dependents_previous as t1')
                    ->join('employees_previous as t2', 't1.employee_id', '=', 't2.id')
                    ->join('companies as t3', 't2.company_id', '=', 't3.id')
                    ->where('t1.last', 'like', '%'.$lastName.'%')
                    ->where('t1.given', 'like', '%'.$firstname.'%')
                    ->orderBy('t1.id', 'DESC')
                    ->limit(1)
                    ->get([
                        't1.given as first_name',
                        't1.middle as middle_name',
                        't1.last as last_name',
                        't3.code as company_code',
                        //'t3.name as company_name',
                    ]);
            }

        $nlist = [];
        if(count($list) > 0) {
            foreach ($list as $key => $row) {
                $nlist[] = [
                    'first_name' => $row->first_name,
                    'middle_name' => $row->middle_name,
                    'last_name' => $row->last_name,
                    //'company_code' => $row->company_code,
                    //'company_name' => $row->company_name,
                    'ebd_comp_code' => $this->GetEbdCode($row->company_code)           
                ];
            }
        }

        return array('list' => $nlist);
    }
}
