<?php

namespace App\Console\Commands\members;

use App\Exports\Members\PendingForSubmissionExport;
use App\Http\Controllers\NotificationController;
use Illuminate\Console\Command;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

// MODELS
use App\Models\Members\hr_members;
use App\Services\SendingEmail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PendingSubmissionCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'pending-for-submission';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'This command will auto run every 2PM';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $timestamp = Carbon::now()->timestamp;
    $yearNow = Carbon::now()->format('Y');
    $monthNow = Carbon::now()->format('M');

    $members = hr_members::query()
      ->with([
        'contact',
        'changePlanPending:id,member_link_id,plan'
      ])
      // ->where('pending_submission_created_at', '>=', Carbon::yesterday()->setTime(14, 1, 0))
      // ->where('pending_submission_created_at', '<=', Carbon::today()->setTime(13, 59, 0))
      // ->pendingApproval()
      ->get();

    if ($members->count() > 0) {
      /**
       * Store the file first before make action in database
       */
      $filename = env('APP_ENV') . "/members/pending-for-submission/$yearNow/$monthNow/PENDING_FOR_SUBMISSION_$timestamp.xlsx";
      $spacesFilename = env('DO_CDN_ENDPOINT') . '/' . $filename;
      $storingSuccess = Excel::store(new PendingForSubmissionExport(['members' => $members]), $filename, 'broadpath');

      if (!$storingSuccess) {
        return response()->json(['message' => 'Uploading file failed.', $spacesFilename]);
      }

      $sending = new SendingEmail(
        email: env('GLEN'),
        body: view('send-pending-for-submission'),
        subject: 'PENDING FOR SUBMISSION',
        attachments: [$spacesFilename],
      );
      $sending->send();

      // $message = [
      //   'body' => view('send-pending-for-submission'),
      //   'attachment' => [$spacesFilename],
      // ];

      // $send = (new NotificationController)->NewMail('', 'glenilagan@llibi.com', $message);

      // if ($success_sending) {
      // foreach ($members as $key => $row) {
      //   hr_members::where('id', $row['id'])
      //     ->update([
      //       'status' => 2,
      //       'changed_status_at' => Carbon::now(),
      //       'excel_batch' => $timestamp,
      //     ]);
      // }

      DB::table('hr_excel_sent_to_philcare')
        ->insert([
          'excel_batch' => $timestamp,
          'pending_submission_path' => $filename,
          'created_at' => Carbon::now()
        ]);
      // }
    }
  }
}
