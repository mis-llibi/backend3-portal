<?php

namespace App\Http\Controllers\Feedback;

use App\Exports\Feedback\CorporateReportExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

use App\Http\Requests\ManualSendFeedbackRequest;
use App\Models\Corporate\Employees;
use App\Models\FeedbackCorporate;
use App\Models\Self_service\Sync;
use App\Services\SendingEmail;
use Exception;
use Illuminate\Http\File;
use Illuminate\Mail\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class FeedbackCorporateController extends Controller
{
  public function index()
  {
    $feedbacks = FeedbackCorporate::query()
      ->join('app_portal_requests as request', 'request.client_id', '=', 'feedbacks_corporate.id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 'feedbacks_corporate.member_id')
      ->select('feedbacks_corporate.*', 'request.loa_type', 'mlist.company_name')
      ->orderBy('feedbacks_corporate.id', 'DESC')->paginate(25);

    $feedback_counts = FeedbackCorporate::query()->count();
    $q1 = $this->getStatisticsQuestOne($feedback_counts);
    $q2 = $this->getStatisticsQuestTwo($feedback_counts);
    $q3 = $this->getStatisticsQuestThree($feedback_counts);
    $q4 = $this->getStatisticsQuestFour($feedback_counts);
    $q5 = $this->getStatisticsQuestFive($feedback_counts);

    return response()->json(['result' => $feedbacks, 'q1' => $q1, 'q2' => $q2, 'q3' => $q3, 'q4' => $q4, 'q5' => $q5]);
  }

  private function getStatisticsQuestOne($feedback_counts)
  {
    $sql = DB::select("
      SELECT SUM(very_easy) AS very_easy, SUM(easy) AS easy, SUM(moderate) AS moderate, 
      SUM(difficult) AS difficult, SUM(very_difficult) AS very_difficult 
      FROM (
        SELECT 
          CASE 
            WHEN question1 = 5 THEN COUNT(question1)
            ELSE 0
            END AS very_easy,
        CASE 
            WHEN question1 = 4 THEN COUNT(question1)
            ELSE 0
            END AS easy,
        CASE 
            WHEN question1 = 3 THEN COUNT(question1)
            ELSE 0
            END AS moderate,
        CASE 
            WHEN question1 = 2 THEN COUNT(question1)
            ELSE 0
            END AS difficult,
        CASE 
            WHEN question1 = 1 THEN COUNT(question1)
            ELSE 0
            END AS very_difficult
        FROM feedbacks_corporate GROUP BY question1)
      AS tbl;
    ");

    $result = [];
    $hundread_percent = 100;
    foreach ($sql as $key => $row) {
      $very_easy = ($row->very_easy / $feedback_counts) * $hundread_percent;
      $easy = ($row->easy / $feedback_counts) * $hundread_percent;
      $moderate = ($row->moderate / $feedback_counts) * $hundread_percent;
      $difficult = ($row->difficult / $feedback_counts) * $hundread_percent;
      $very_difficult = ($row->very_difficult / $feedback_counts) * $hundread_percent;
      array_push($result, [
        'very_easy' => number_format($very_easy, 2),
        'easy' => number_format($easy, 2),
        'moderate' => number_format($moderate, 2),
        'difficult' => number_format($difficult, 2),
        'very_difficult' => number_format($very_difficult, 2),
      ]);
    }

    return $result;
  }

  private function getStatisticsQuestTwo($feedback_counts)
  {
    $sql = DB::select("
      SELECT SUM(yes) AS yes, SUM(no) AS no
      FROM (
        SELECT 
          CASE 
            WHEN question2 = 1 THEN COUNT(question2)
            ELSE 0
            END AS yes,
        CASE 
            WHEN question2 = 0 THEN COUNT(question2)
            ELSE 0
            END AS no
        FROM feedbacks_corporate GROUP BY question2)
      AS tbl;
    ");

    $result = [];
    $hundread_percent = 100;
    foreach ($sql as $key => $row) {
      $yes = ($row->yes / $feedback_counts) * $hundread_percent;
      $no = ($row->no / $feedback_counts) * $hundread_percent;
      array_push($result, [
        'yes' => number_format($yes, 2),
        'no' => number_format($no, 2),
      ]);
    }

    return $result;
  }

  private function getStatisticsQuestThree($feedback_counts)
  {
    $sql = DB::select("
      SELECT SUM(yes) AS yes, SUM(no) AS no
      FROM (
        SELECT 
          CASE 
            WHEN question3 = 1 THEN COUNT(question3)
            ELSE 0
            END AS yes,
        CASE 
            WHEN question3 = 0 THEN COUNT(question3)
            ELSE 0
            END AS no
        FROM feedbacks_corporate GROUP BY question3)
      AS tbl;
    ");

    $result = [];
    $hundread_percent = 100;
    foreach ($sql as $key => $row) {
      $yes = ($row->yes / $feedback_counts) * $hundread_percent;
      $no = ($row->no / $feedback_counts) * $hundread_percent;
      array_push($result, [
        'yes' => number_format($yes, 2),
        'no' => number_format($no, 2),
      ]);
    }

    return $result;
  }

  private function getStatisticsQuestFour($feedback_counts)
  {
    $sql = DB::select("
      SELECT SUM(very_easy) AS very_easy, SUM(easy) AS easy, SUM(moderate) AS moderate, 
      SUM(difficult) AS difficult, SUM(very_difficult) AS very_difficult 
      FROM (
        SELECT 
          CASE 
            WHEN question4 = 5 THEN COUNT(question4)
            ELSE 0
            END AS very_easy,
        CASE 
            WHEN question4 = 4 THEN COUNT(question4)
            ELSE 0
            END AS easy,
        CASE 
            WHEN question4 = 3 THEN COUNT(question4)
            ELSE 0
            END AS moderate,
        CASE 
            WHEN question4 = 2 THEN COUNT(question4)
            ELSE 0
            END AS difficult,
        CASE 
            WHEN question4 = 1 THEN COUNT(question4)
            ELSE 0
            END AS very_difficult
        FROM feedbacks_corporate GROUP BY question4)
      AS tbl;
    ");

    $result = [];
    $hundread_percent = 100;
    foreach ($sql as $key => $row) {
      $very_easy = ($row->very_easy / $feedback_counts) * $hundread_percent;
      $easy = ($row->easy / $feedback_counts) * $hundread_percent;
      $moderate = ($row->moderate / $feedback_counts) * $hundread_percent;
      $difficult = ($row->difficult / $feedback_counts) * $hundread_percent;
      $very_difficult = ($row->very_difficult / $feedback_counts) * $hundread_percent;
      array_push($result, [
        'very_easy' => number_format($very_easy, 2),
        'easy' => number_format($easy, 2),
        'moderate' => number_format($moderate, 2),
        'difficult' => number_format($difficult, 2),
        'very_difficult' => number_format($very_difficult, 2),
      ]);
    }

    return $result;
  }

  private function getStatisticsQuestFive($feedback_counts)
  {
    $sql = DB::select("
      SELECT SUM(very_easy) AS very_easy, SUM(easy) AS easy, SUM(moderate) AS moderate, 
      SUM(difficult) AS difficult, SUM(very_difficult) AS very_difficult 
      FROM (
        SELECT 
          CASE 
            WHEN question5 = 5 THEN COUNT(question5)
            ELSE 0
            END AS very_easy,
        CASE 
            WHEN question5 = 4 THEN COUNT(question5)
            ELSE 0
            END AS easy,
        CASE 
            WHEN question5 = 3 THEN COUNT(question5)
            ELSE 0
            END AS moderate,
        CASE 
            WHEN question5 = 2 THEN COUNT(question5)
            ELSE 0
            END AS difficult,
        CASE 
            WHEN question5 = 1 THEN COUNT(question5)
            ELSE 0
            END AS very_difficult
        FROM feedbacks_corporate GROUP BY question5)
      AS tbl;
    ");

    $result = [];
    $hundread_percent = 100;
    foreach ($sql as $key => $row) {
      $very_easy = ($row->very_easy / $feedback_counts) * $hundread_percent;
      $easy = ($row->easy / $feedback_counts) * $hundread_percent;
      $moderate = ($row->moderate / $feedback_counts) * $hundread_percent;
      $difficult = ($row->difficult / $feedback_counts) * $hundread_percent;
      $very_difficult = ($row->very_difficult / $feedback_counts) * $hundread_percent;
      array_push($result, [
        'very_easy' => number_format($very_easy, 2),
        'easy' => number_format($easy, 2),
        'moderate' => number_format($moderate, 2),
        'difficult' => number_format($difficult, 2),
        'very_difficult' => number_format($very_difficult, 2),
      ]);
    }

    return $result;
  }

  function sendLoa(ManualSendFeedbackRequest $request)
  {
    $employee_id = $request->employee_id;
    $approval_code = $request->approval_code ?? '';
    $email = $request->email;
    $provider_email = $request->provider_email;
    $loa = $request->file('loa');
    $company_id = $request->company_id;

    // $path = $request->file('loa')->storeAs(
    //   'public/corporate/upload/loa',
    //   $loa->getClientOriginalName()
    // );

    $path = [];
    foreach ($loa as $key => $row) {
      # code...
      $original_name = $row->getClientOriginalName();
      // Log::info($original_name);

      $save_path = $row->storeAs('client-portal/' . env('APP_ENV') . '/sent/loa/' . $request->email_format_type . '/' . $company_id . '/' . $employee_id, $original_name, 'llibiapp');
      // Log::info($path);

      array_push(
        $path,
        env('DO_LLIBI_URL') . '/' . $save_path
      );
    }


    $employee = Employees::where('id', $employee_id)->first();

    abort_if(!$employee, 404, 'Employee Not Found.');

    $masterlist = Sync::query()
      ->where('empcode', $employee->code)
      ->whereDate('birth_date', $employee->birthdate)
      ->first();

    abort_if(!$employee, 404, 'Member Not Found.');

    $q = Str::random(64);
    $company_code = $masterlist->company_code;
    $member_id = $masterlist->member_id;
    $feedback_url = env('FRONTEND_URL') . "/feedback/corporate?q=$q&company_code=$company_code&member_id=$member_id&approval_code=$approval_code";

    $isAdmu = in_array($company_id, [91, 92, 94, 371, 411]);

    $viewTemplate = 'send-corporate-loa';
    $statusRemarks = '';
    switch ($request->email_format_type) {
      case 'consultation':
        if ($isAdmu) {
          $viewTemplate = 'admu-corporate.send-standalone';
        } else {
          $statusRemarks = '<p>Your LOA request is <b>approved</b>. Please print a copy LOA and present to the accredited provider upon availment.</p>';
        }
        break;
      case 'laboratory':
        if ($isAdmu) {
          $viewTemplate = 'admu-corporate.send-standalone';
        } else {
          $statusRemarks = '
              <p>Please print a copy of LOA and present to the accredited provider upon availment.</p> 
              <p>Outpatient Procedure LOA is subject for Client Care’s approval based on doctor’s laboratory referral and evaluation of the diagnosis.</p>';
          /* $statusRemarks = '
          <p>Your LOA request is <b>approved</b>. Please print a copy of LOA and present to the accredited provider upon availment with doctor’s laboratory referral.</p>  
          <p>This is a pre-approved Outpatient Procedure LOA with approval code for guaranteed amount indicated. If the guaranteed amount is less than the actual laboratory cost or there are additional laboratory procedures as advised by the doctor, please contact our Client Care Hotline for re-approval.</p>'; */
        }
        break;
      case '2n1-standalone':
        if ($isAdmu) {
          $viewTemplate = 'admu-corporate.send-standalone';
        } else {
          $statusRemarks = '
              <p>Please print a copy of LOA and present to the accredited provider upon availment.</p> 
              <p>Consultation LOA is pre-approved. Outpatient Procedure LOA is subject for Client Care’s approval based on doctor’s laboratory referral and evaluation of the diagnosis.</p>';
        }
        break;
      case 'pre-approved-laboratory':
        if ($isAdmu) {
          $viewTemplate = 'admu-corporate.send-pre-approved-laboratory';
        } else {
          $statusRemarks = '
              <p>Please print a copy of LOA and present to the accredited provider upon availment with doctor’s laboratory referral.</p> 
              <p>This is a pre-approved Outpatient Procedure LOA with approval code for guaranteed amount indicated. If the guaranteed amount is less than the actual laboratory cost or there are additional laboratory procedures as advised by the doctor, please contact our Client Care Hotline for re-approval.</p>';
        }
        break;

      default:
        throw new Exception("Email format is not supported", 1);
        break;
    }

    $mailMsg = view($viewTemplate, [
      'homepage' => env('FRONTEND_URL'),
      'first_name' => $masterlist->first_name,
      'feedback_url' => $feedback_url,
      'status_remarks' => $statusRemarks,
    ]);

    if (App::environment('local')) {
      $emailer = new SendingEmail(
        email: 'glenilagan@llibi.com',
        body: $mailMsg,
        subject: 'LACSON & LACSON CLIENT CARE NOTIFICATION',
        attachments: $path,
      );
      $emailer->send();

      // if ($provider_email) {
      //   $emailer = new SendingEmail(
      //     email: $provider_email,
      //     body: $mailMsg,
      //     subject: 'LLIBI LOA TO PROVIDER',
      //     attachments: [Storage::path($path)],
      //   );
      //   $emailer->send();
      // }
    }

    if (App::environment('production')) {
      // $mailMsg['cc'] = 'clientcare@llibi.com';
      // $mailMsg['subject'] = 'CORPORATE REQUEST LOA';
      // $mail = (new NotificationController)->NewMail('', $email, $mailMsg);
      $emailer = new SendingEmail(
        email: $email,
        body: $mailMsg,
        subject: 'LACSON & LACSON CLIENT CARE NOTIFICATION',
        attachments: $path,
        cc: ['clientcare@llibi.com'],
      );
      $emailer->send();

      if ($provider_email) {
        // $mailMsg['subject'] = 'LLIBI LOA TO PROVIDER';
        // $mail = (new NotificationController)->NewMail('', $provider_email, $mailMsg);
        $emailer = new SendingEmail(
          email: $provider_email,
          body: $mailMsg,
          subject: 'LACSON & LACSON CLIENT CARE NOTIFICATION',
          attachments: $path,
        );
        $emailer->send();
      }
    }

    return response()->noContent();
  }

  public function store(Request $request)
  {
    // $employee = Employees::where('id', $request->employee_id)->first();
    // if (!$employee) {
    //   return response()->json(['status' => false, 'message' => 'Employee Not Found.'], 404);
    // }

    $masterlist = Sync::query()
      ->where('member_id', $request->member_id)
      // ->whereDate('birth_date', $employee->birthdate)
      ->first();

    if (!$masterlist) {
      return response()->json(['status' => false, 'message' => 'Member Not Found.'], 404);
    }

    if (FeedbackCorporate::where('approval_code', $request->approval_code)->where('member_id', $request->member_id)->exists()) {
      return response()->json(['status' => false, 'message' => 'Already sent feedback.'], 400);
    }

    $feedback = new FeedbackCorporate();
    $feedback->company_code = $request->company_code;
    $feedback->member_id = $masterlist->member_id;
    $feedback->approval_code = $request->approval_code;
    $feedback->question1 = $request->questionOne;
    $feedback->question2 = $request->questionTwo;
    $feedback->question3 = $request->questionThree;
    $feedback->question4 = $request->questionFour;
    $feedback->question5 = $request->questionFive;
    $feedback->comments = $request->comments;
    $feedback->save();

    return response()->json(['status' => true, 'message' => 'Successfully submit.', 'feedback' => $feedback]);
  }

  public function showEmployee()
  {
    $employee_id = request()->query('employee_id');
    $employee = Employees::where('id', $employee_id)->first();
    // if (!$employee) {
    //   return response()->json(['status' => false, 'message' => 'Employee Not Found.'], 404);
    // }

    abort_if(!$employee, 404, 'Employee Not Found.');

    $masterlist = Sync::query()
      ->where('empcode', $employee->code)
      ->whereDate('birth_date', $employee->birthdate)
      ->first();

    // if (!$masterlist) {
    //   return response()->json(['status' => false, 'message' => 'Member Not Found.'], 404);
    // }
    abort_if(!$masterlist, 404, 'Member Not Found.');

    return response()->json($masterlist);
  }

  public function exportFeedbackReport(Request $request)
  {
    return (new CorporateReportExport)->download('feedback-corporate-report.csv');
  }
}
