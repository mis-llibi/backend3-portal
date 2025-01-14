<?php

namespace App\Http\Controllers\PreApprove;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\PreApprove\LaboratoryLogs;
use App\Models\PreApprove\PreApprovedLogs;
use App\Models\PreApprove\UtilizationLogs;

class PreApprovedLogsController extends Controller
{
  public function store(Request $request)
  {
    return DB::transaction(function () use ($request) {
      $employee = $request->employee;
      $laboratory = collect($request->laboratory);
      $utilization = collect($request->utilization);
      $preapproved_details = $request->preapproved_details;
      $mbl_amount = $request->mbl_amount;
      $laboratory_amount = $request->laboratory_amount;
      $utilization_amount = $request->utilization_amount;

      $inputs = [
        'member_id' => $employee['masterlist']['member_id'],
        'company' => $employee['masterlist']['company_name'],
        'plan_type' => $employee['companies']['plantype'] . ' ' . $employee['companies']['sharetype'],
        'mbl' => $mbl_amount,
        'reservation' => $employee['reserving_amount'],
        'utilization' => $laboratory_amount,
        'laboratory' => $utilization_amount,
      ];

      $preapproved = PreApprovedLogs::create($inputs);

      if ($utilization->count() > 0) {
        foreach ($utilization as $key => $row) {
          UtilizationLogs::create([
            'pre_approved_log_id' => $preapproved->id,
            'member_id' => $preapproved->member_id,
            'diagname' => $row['diagname'],
            'amount' => $row['eligible'],
          ]);
        }
      }

      if ($laboratory->count() > 0) {
        foreach ($laboratory as $key => $row) {
          LaboratoryLogs::create([
            'pre_approved_log_id' => $preapproved->id,
            'member_id' => $preapproved->member_id,
            'laboratory' => $row['laboratory'],
            'amount' => $employee['hospital']['hospitalclass'] === 1 ? $row['cost'] : $row['cost2'],
          ]);
        }
      }

      return $preapproved;
    });
  }
}
