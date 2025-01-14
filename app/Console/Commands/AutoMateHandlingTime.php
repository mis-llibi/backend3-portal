<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoMateHandlingTime extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'command:automate-handling-time';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $request = DB::table('app_portal_clients as t1')
      ->join('app_portal_requests as t2', 't2.client_id', '=', 't1.id')
      ->leftJoin('users as user', 'user.id', '=', 't1.user_id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 't1.member_id')
      ->select(
        't1.id',
        't1.created_at as createdAt',
        't1.approved_date',
      )
      ->whereNotNull('t1.approved_date')
      ->where('t1.handling_time', 0);


    $request = $request->orderBy('t1.id', 'DESC')->get();

    foreach ($request as $key => $row) {
      $tat = 0;
      if ($row->approved_date) {
        $created_at = Carbon::parse($row->createdAt, 'Asia/Manila');
        $approved_at = Carbon::parse($row->approved_date, 'Asia/Manila');

        $valid_start = Carbon::parse('06:00'); // 6:00 AM
        $valid_end = Carbon::parse('18:00');   // 6:00 PM

        if ($created_at->isYesterday() && $approved_at->isToday()) {
          if ($created_at->format('H:i') > $valid_start->format('H:i') && $created_at->format('H:i') < $valid_end->format('H:i')) {
            $diff = Carbon::parse($created_at->format('H:i'))->diffInMinutes($valid_end);
            // Log::info('created ' . $diff);
            $tat += $diff;
          }

          // if ($approved_at->greaterThan($valid_start) && $approved_at->lessThan($valid_end)) {
          if ($approved_at->format('H:i') > $valid_start->format('H:i') && $approved_at->format('H:i') < $valid_end->format('H:i')) {
            $diff = Carbon::parse($valid_start)->diffInMinutes($approved_at->format('H:i'));
            // Log::info('approved ' . $diff);
            $tat += $diff;
          }
        }

        if ($created_at->isToday() && $approved_at->isToday()) {
          if ($created_at->format('H:i') >= $valid_start->format('H:i') && $created_at->format('H:i') <= $valid_end->format('H:i')) {
            $diff = Carbon::parse($created_at)->diffInMinutes($approved_at);
            $tat += $diff;
          }
        }
      }

      DB::table('app_portal_clients')->where('id', $row->id)->update(['handling_time' => $tat]);
    }
    // Log::info($request);
  }
}
