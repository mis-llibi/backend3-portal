<?php

namespace App\Http\Controllers\PreApprove;

use App\Http\Controllers\Controller;
use App\Imports\Deel\DepsImport;
use App\Imports\PreApprove\CorporateClaimsImport;
use Illuminate\Http\Request;

use App\Models\PreApprove\Claims;
use Maatwebsite\Excel\Facades\Excel;

class ClaimsController extends Controller
{
  public function index(Request $request)
  {
    $dd = Claims::where('claimnumb', '0000016394')->first();

    return $dd->diagname;
  }

  function importClaims(Request $request)
  {
    /**
     * NOTE:
     * The file must be csv and save as utf8 format
     * click file, save as, more options, tools, web options, encoding set to utf-8
     */
    Excel::import(new CorporateClaimsImport, $request->file);

    return 'done';
  }

  function importDeelUpload(Request $request)
  {
    Excel::import(new DepsImport, $request->file);

    return 'done';
  }
}
