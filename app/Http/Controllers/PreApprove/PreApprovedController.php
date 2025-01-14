<?php

namespace App\Http\Controllers\PreApprove;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Corporate\Companies;
use App\Models\Corporate\Dependents;
use App\Models\Corporate\Employees;
use App\Models\Corporate\LoaDependent;
use App\Models\Corporate\LoaEmployee;
use App\Models\Corporate\UtilizationLoa;
use App\Models\PreApprove\Laboratory;

use App\Models\PreApprove\Utilization;
use App\Models\Self_service\Sync;
use Carbon\Carbon;

use App\Traits\PreApprovedDetailsTrait;
use Illuminate\Support\Facades\Log;

class PreApprovedController extends Controller
{
  use PreApprovedDetailsTrait;

  public function getEmployee(Request $request)
  {
    $employee_id = $request->query('employee_id', null);
    $patient_id = $request->query('patient_id', null);
    $company_id = $request->query('company_id', null);
    $hospital_id = $request->query('hospital_id', null);

    $company = Companies::where('id', $company_id)->select('code')->first();

    abort_if(!$employee_id, '400', 'Something went wrong. Please check employee id');
    abort_if(!$patient_id, '400', 'Something went wrong. Please check patient id');
    abort_if(!$company_id || !$company, '400', 'Something went wrong. Please check company id');


    switch ($company->code) {
      case 'PCOR':
        abort('400', 'As Charged, No limit for Petron laboratory. Please close the window');
        break;
    }

    if ($employee_id != $patient_id) {
      //dependents

      $dependents = Dependents::where('id', $patient_id)
        ->where('employee_id', $employee_id)
        ->select('id', 'employee_id', 'code', 'given', 'middle', 'last', 'ipr', 'opr', 'birthdate', 'inscode')
        ->first();

      abort_if(!$dependents, 404, 'Dependents not found.');

      $deps = $this->dependentPreApprovedDetails($dependents, $hospital_id);

      return response()->json($deps);
    } else {
      // employee

      $employees = Employees::where('id', $patient_id)
        ->select('id', 'company_id', 'code', 'given', 'middle', 'last', 'ipr', 'opr', 'birthdate')
        ->first();

      abort_if(!$employees, 404, 'Employee not found.');

      $emps = $this->employeePreApprovedDetails($employees, $hospital_id);

      return response()->json($emps);
    }

    abort(400, 'Something went wrong.');
  }
}
