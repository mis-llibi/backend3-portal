<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Http\Controllers\Self_enrollment\ManageBroadpathEnrollee;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    /**
     * run only in production when APP_ENV is production
     */
    if (App::environment(['production'])) {
      $schedule->call(function () {
        $dateToday = '2023-04-02'; //date('Y-m-d');
        $dateFinalWarning = '2023-04-05';
        $dateFormLocked = '2023-04-06';

        $reminder = (new ManageBroadpathEnrollee)->checkReminders($dateToday, $dateFinalWarning, $dateFormLocked);
      })->dailyAt('07:00');

      $schedule->command('command:automate-handling-time')->everyMinute();
      $schedule->command('send:pending-not-moving')->everyMinute();
      $schedule->call(function () {
        Storage::deleteDirectory('public/manual/upload/loa');
      })->everyMinute();
    }

    /**
     * @see \App\Console\Commands\members\PendingSubmissionCommand
     * should run everyday at 2PM change it.
     */
    // $schedule->command('pending-for-submission')->everyMinute();

    // CHEKCING STATUS OF INFOBIP IF WORKING
    $schedule->command('checking-infobip-status')->everySixHours();
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
