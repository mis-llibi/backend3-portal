<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\Members\hr_members;

use App\Http\Requests\Member\ApproveMemberDeletionRequest;
use App\Http\Requests\Member\ApproveMemberRequest;
use App\Enums\Broadpath\Members\StatusEnum;
use App\Models\Members\PendingDocument;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
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

  public function approveMember(ApproveMemberRequest $request, $id)
  {
    $member = hr_members::find($id);

    abort_if(!$member, 404, 'Member not found.');

    $principal = hr_members::query()->where('member_id', $member->member_id)->principal()->first();

    if (is_null($principal->certificate_no) && $member->relationship_id != 'PRINCIPAL') {
      return response()->json(['message' => 'The principal should have Certificate No. before approving dependents.', 'data' => $member], 400);
    }

    $member->certificate_no = $request->certificate_no;
    // $member->certificate_issued_at = $request->certificate_issued_at;
    $member->certificate_issued_at = Carbon::now();
    $member->status = 4;
    $member->approved_member_at = Carbon::now();
    $member->approved_by = Auth::id();
    $member->save();

    return response()->json(['message' => 'Approve member success.', 'data' => $member]);
  }

  public function approveDeletion(ApproveMemberDeletionRequest $request, $id)
  {
    $member = hr_members::find($id);
    $member->status = 7;
    $member->admin_remarks = $request->remarks;
    $member->approved_deleted_member_at = Carbon::parse($request->approved_deleted_member_at)->format('Y-m-d H:i');
    $member->approved_deleted_member_at_original = Carbon::now();
    $member->approved_by = Auth::id();
    $member->save();

    return response()->json(['message' => 'Approve deletion success.', 'data' => $member]);
  }

  public function approveChangePlan(Request $request, $id)
  {
    $member = hr_members::find($id);
    $member->status = 9;
    $member->plan = $request->plan;
    $member->approved_change_plan_at = Carbon::now();
    $member->approved_by = Auth::id();
    $member->save();

    return response()->json(['message' => 'Approve change plan success.', 'data' => $member]);
  }

  public function disapproveMember(ApproveMemberDeletionRequest $request, $id)
  {
    $member = hr_members::find($id);
    $member->status = 10;
    $member->admin_remarks = $request->remarks;
    $member->approved_deleted_member_at = Carbon::parse($request->approved_deleted_member_at)->format('Y-m-d H:i');
    $member->approved_deleted_member_at_original = Carbon::now();
    $member->approved_by = Auth::id();
    $member->save();

    return response()->json(['message' => 'Disapprove member success.', 'data' => $member]);
  }

  public function approveEditInformation(Request $request, $id)
  {
    $member = hr_members::find($id);
    $member->first_name = $request->first_name;
    $member->last_name = $request->last_name;
    $member->middle_name = $request->filled('middle_name');
    $member->birth_date = Carbon::parse($request->birth_date)->format('Y-m-d');
    $member->status = StatusEnum::APPROVED_CORRECTION->value;
    $member->approved_correction_at = Carbon::now();
    $member->save();

    $member->contact->update([
      'email' => $request->email,
      'approved_correction_at' => Carbon::now()
    ]);

    return response()->json(['message' => 'Success update information', 'data' => $member]);
  }

  public function requestPendingDocuments(Request $request): JsonResponse
  {
    $member_id = $request->member_id ?? null;
    $bc = $request->bc ?? null;
    $mc = $request->mc ?? null;
    $ceno = $request->ceno ?? null;
    $cocoha = $request->cocoha ?? null;
    $dc = $request->dc ?? null;
    $divo = $request->divo ?? null;
    $coohic = $request->coohic ?? null;
    $cowea = $request->cowea ?? null;
    $oth = $request->oth ?? null;
    $other_document = $request->other_document ?? null;

    $payload = array_filter([
      $bc ? ['link_id' => $member_id, 'file_required' => $bc] : null,
      $mc ? ['link_id' => $member_id, 'file_required' => $mc] : null,
      $ceno ? ['link_id' => $member_id, 'file_required' => $ceno] : null,
      $cocoha ? ['link_id' => $member_id, 'file_required' => $cocoha] : null,
      $dc ? ['link_id' => $member_id, 'file_required' => $dc] : null,
      $divo ? ['link_id' => $member_id, 'file_required' => $divo] : null,
      $coohic ? ['link_id' => $member_id, 'file_required' => $coohic] : null,
      $cowea ? ['link_id' => $member_id, 'file_required' => $cowea] : null,
      $oth ? ['link_id' => $member_id, 'file_required' => $other_document] : null,
    ]);

    foreach ($payload as $key => $item) {
      PendingDocument::create($item);
    }

    hr_members::where('id', $member_id)->update(['status' => 11]);

    $pendig_documents = PendingDocument::where('link_id', $member_id)->get();

    return response()->json($pendig_documents);
  }
}
