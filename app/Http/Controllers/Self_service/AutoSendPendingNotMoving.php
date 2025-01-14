<?php

namespace App\Http\Controllers\Self_service;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\ReportSetting;

class AutoSendPendingNotMoving extends Controller
{
  public function autoSendEmail()
  {
    $get_minutes = ReportSetting::query()->first();
    $less_minutes = Carbon::now()->subMinutes($get_minutes->minutes)->toTimeString();
    $date_today = Carbon::now()->toDateString();

    $request = DB::table('app_portal_clients as t1')
      ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 't1.member_id')
      ->select(
        't1.id',
        't1.reference_number as refno',
        't1.email as email',
        't1.alt_email as altEmail',
        't1.contact as contact',
        't1.member_id as memberID',
        't1.first_name as firstName',
        't1.last_name as lastName',
        't1.dob as dob',
        't1.remarks as remarks',
        't1.status as status',
        't1.created_at as createdAt',
        't1.approved_date',
        DB::raw('TIMESTAMPDIFF(MINUTE, t1.created_at, t1.approved_date) as elapse_minutes'),
        DB::raw('TIMESTAMPDIFF(HOUR, t1.created_at, t1.approved_date) as elapse_hours'),
        'mlist.company_name',
        't1.is_sent'
      )
      ->where('t1.status', 2)
      ->whereDate('t1.created_at', '<=', $date_today)
      ->whereTime('t1.created_at', '>=', $less_minutes)
      ->where('is_sent', 0)
      ->orderBy('t1.id', 'DESC')
      ->get();

    return $request;
  }
}
