<?php

namespace App\Traits;

use App\Models\Corporate\Companies;
use App\Models\Corporate\Employees;
use App\Models\Corporate\Hospitals;
use App\Models\Corporate\LoaDependent;
use App\Models\Corporate\LoaEmployee;
use App\Models\Corporate\UtilizationLoa;
use App\Models\PreApprove\Laboratory;
use App\Models\PreApprove\Utilization;
use App\Models\Self_service\Sync;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

trait PreApprovedDetailsTrait
{
  private function dependentPreApprovedDetails($dependents, $hospital_id)
  {
    $employees = Employees::where('id', $dependents->employee_id)
      ->select('id', 'company_id', 'code', 'given', 'middle', 'last', 'ipr', 'opr', 'birthdate')
      ->first();

    $companies = Companies::where('id', $employees->company_id)
      ->select('id', 'code', 'name', 'plantype', 'sharetype')
      ->first();

    $masterlist = $this->getMasterList($employees->code, $dependents->birthdate);

    abort_if(!$masterlist, '404', 'Masterlist Not Found.');

    $utilization = Utilization::query()->where('compcode', $masterlist->company_code);
    // Plan 1: base uniqcode
    // Plan 2: base empcode per family
    // Plan 3: base empcode except relation employee
    if ($masterlist->plantype == 1) {
      $utilization = $utilization->where('uniqcode', $masterlist->member_id)->get();
    } else if ($masterlist->plantype == 2) {
      $utilization = $utilization->where('empcode', $masterlist->empcode)->get();
    } else {
      // 3
      $utilization = $utilization->where('empcode', $masterlist->empcode)
        ->where('relation', '!=', 'EMPLOYEE')
        ->get();
    }

    $laboratory = Laboratory::query()->get();

    $loaUtilization = UtilizationLoa::query()
      ->whereIn('status', [1, 2])
      ->where('patcode', $dependents->inscode)
      ->where('companycode', $companies->code)
      ->get();

    $reserving_amount = 0;

    if ($loaUtilization) {
      foreach ($loaUtilization as $key => $row) {
        $loa_dependent = LoaDependent::query()
          ->where('dependent_id', $dependents->id)
          ->where(function ($query) use ($row) {
            $query->whereDate('incepfrom', '<=', Carbon::parse($row->dateissued)->format('Y-m-d'));
            $query->whereDate('incepto', '>=', Carbon::parse($row->dateissued)->format('Y-m-d'));
          })
          ->first();

        if ($loa_dependent) {
          $reserving_amount += $row->amount;
        }
      }
    }

    $employees['companies'] = $companies;
    $employees['plan_type'] = $masterlist->plantype;
    $employees['utilization'] = $utilization;
    $employees['laboratory'] = $laboratory;
    // $employees['loa_util'] = $loaUtilization;
    $employees['reserving_amount'] = $masterlist->plantype === 2 ? '5542.00' : $reserving_amount;
    $employees['masterlist'] = $masterlist;
    $employees['hospital'] = $this->getHospital($hospital_id);

    return $employees;
  }

  private function employeePreApprovedDetails($employees, $hospital_id)
  {
    $companies = Companies::where('id', $employees->company_id)
      ->select('id', 'code', 'name', 'plantype', 'sharetype')
      ->first();

    $masterlist = $this->getMasterList($employees->code, $employees->birthdate);

    abort_if(!$masterlist, '404', 'Masterlist Not Found.');

    $utilization = Utilization::query()->where('compcode', $masterlist->company_code);
    // Plan 1: base uniqcode
    // Plan 2: base empcode per family
    // Plan 3: base empcode except relation employee
    if ($masterlist->plantype == 1) {
      $utilization = $utilization->where('uniqcode', $masterlist->member_id)->get();
    } else if ($masterlist->plantype == 2) {
      $utilization = $utilization->where('empcode', $masterlist->empcode)->get();
    } else {
      // 3
      $utilization = $utilization->where('empcode', $masterlist->empcode)
        ->where('relation', '!=', 'EMPLOYEE')
        ->get();
    }

    $laboratory = Laboratory::query()->get();

    $loaUtilization = UtilizationLoa::query()
      ->whereIn('status', [1, 2])
      ->where('patcode', $employees->code)
      ->where('companycode', $companies->code)
      ->get();

    Log::debug($employees);
    Log::debug($loaUtilization);

    $reserving_amount = 0;

    if (count($loaUtilization) > 0) {
      foreach ($loaUtilization as $key => $row) {
        // $loa_employee = LoaEmployee::query()
        //   ->where('employee_id', $employees->id)
        //   ->where(function ($query) use ($row) {
        //     $query->whereDate('incepfrom', '<=', Carbon::parse($row->dateissued)->format('Y-m-d'));
        //     $query->whereDate('incepto', '>=', Carbon::parse($row->dateissued)->format('Y-m-d'));
        //   })
        //   ->first();

        // if ($loa_employee) {
        $reserving_amount += $row->amount;
        // }
      }
    }

    $employees['companies'] = $companies;
    $employees['plan_type'] = $masterlist->plantype;
    $employees['utilization'] = $utilization;
    $employees['laboratory'] = $laboratory;
    // $employees['loa_util'] = $loaUtilization;
    $employees['reserving_amount'] = $reserving_amount;
    $employees['masterlist'] = $masterlist;
    $employees['hospital'] = $this->getHospital($hospital_id);

    return $employees;
  }

  private function getMasterList($empcode, $birthdate)
  {
    return Sync::query()
      ->whereDate('birth_date', Carbon::parse($birthdate)->format('Y-m-d'))
      ->where('empcode', $empcode)
      ->first();
  }

  private function getHospital($hospital_id)
  {
    return Hospitals::query()->where('id', $hospital_id)->first();
  }
}
