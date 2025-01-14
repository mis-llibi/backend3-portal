<?php

namespace App\Http\Controllers\PreApprove;

use App\Http\Controllers\Controller;
use App\Imports\Deel\DepsImport;
use App\Imports\PreApprove\UtilizationImport;
use Illuminate\Http\Request;

use App\Models\PreApprove\Utilization;
use Maatwebsite\Excel\Facades\Excel;

class UtilizationController extends Controller
{
  public function index(Request $request)
  {
    $dd = Utilization::where('claimnumb', '0000016394')->first();

    return $dd->diagname;
  }

  function importUtilization(Request $request)
  {
    set_time_limit(0);
    foreach ($request->file as $key => $file) {
      Excel::import(new UtilizationImport, $file);
    }

    return 'done';
  }

  function importDeelUpload(Request $request)
  {
    Excel::import(new DepsImport, $request->file);

    return 'done';
  }
}
