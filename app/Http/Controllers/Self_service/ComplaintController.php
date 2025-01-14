<?php

namespace App\Http\Controllers\Self_service;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\Self_service\Complaint;

class ComplaintController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $complaint = Complaint::query()->latest()->get();

    return response()->json($complaint);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $complaint = Complaint::create([
      'uuid' => Str::uuid(),
      'last_name' => $request->last_name,
      'first_name' => $request->first_name,
      'middle_name' => $request->middle_name,
      'dob' => $request->dob ? Carbon::parse($request->dob)->format('Y-m-d') : null,
      'email' => $request->email,
      'ercard_no' => $request->ercard_no,
      'company_name' => $request->company_name,

      'deps_last_name' => $request->deps_last_name,
      'deps_first_name' => $request->deps_first_name,
      'deps_dob' => $request->deps_dob ? Carbon::parse($request->deps_dob)->format('Y-m-d') : null,
      'deps_ercard_no' => $request->deps_ercard_no,
    ]);


    $body = ['body' => view('send-complaint', ['complaint' => $complaint]), 'subject' => 'COMPLAINT REQUEST'];
    (new NotificationController)->EncryptedPDFMailNotification('', 'glenilagan@llibi.com', $body);

    return response()->json($complaint);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $complaint = Complaint::where('uuid', $id)->first();

    return response()->json($complaint);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
