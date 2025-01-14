<?php

namespace App\Http\Controllers\Feedback;

use App\Exports\Feedback\ClientCareReportExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

use App\Models\Feedback;
use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\ManualSendFeedbackRequest;
use App\Models\Corporate\Employees;
use App\Models\Self_service\Client;
use App\Models\Self_service\Sync;
use App\Services\SendingEmail;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Mail\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
  public function index()
  {
    $feedbacks = Feedback::query()
      ->join('app_portal_requests as request', 'request.client_id', '=', 'feedbacks.id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 'feedbacks.member_id')
      ->select('feedbacks.*', 'request.loa_type', 'mlist.company_name')
      ->orderBy('feedbacks.id', 'DESC')->paginate(25);

    $feedback_counts = Feedback::query()->count();
    $q1 = $this->getStatisticsQuestOne($feedback_counts);
    $q2 = $this->getStatisticsQuestTwo($feedback_counts);
    $q3 = $this->getStatisticsQuestThree($feedback_counts);
    $q4 = $this->getStatisticsQuestFour($feedback_counts);

    return response()->json(['result' => $feedbacks, 'q1' => $q1, 'q2' => $q2, 'q3' => $q3, 'q4' => $q4]);
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
        FROM feedbacks GROUP BY question1)
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
      SELECT SUM(very_easy) AS very_easy, SUM(easy) AS easy, SUM(moderate) AS moderate, 
      SUM(difficult) AS difficult, SUM(very_difficult) AS very_difficult 
      FROM (
        SELECT 
          CASE 
            WHEN question2 = 5 THEN COUNT(question2)
            ELSE 0
            END AS very_easy,
        CASE 
            WHEN question2 = 4 THEN COUNT(question2)
            ELSE 0
            END AS easy,
        CASE 
            WHEN question2 = 3 THEN COUNT(question2)
            ELSE 0
            END AS moderate,
        CASE 
            WHEN question2 = 2 THEN COUNT(question2)
            ELSE 0
            END AS difficult,
        CASE 
            WHEN question2 = 1 THEN COUNT(question2)
            ELSE 0
            END AS very_difficult
        FROM feedbacks GROUP BY question2)
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
        FROM feedbacks GROUP BY question3)
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
        FROM feedbacks GROUP BY question4)
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

  public function store(FeedbackRequest $request)
  {

    $llibixadmu_key = $request->header('X-LLIBIXADMU-KEY');

    if ($llibixadmu_key !== env('LLIBIXADMU_KEY') && $request->member_id === 'MANUAL' && $request->company_code === 'admu') {
      abort(401, 'Unauthorized api key');
    }

    $comment = $request->comment ?? "";
    $questionOne = $request->questionOne;
    $questionTwo = $request->questionTwo;
    $questionThree = $request->questionThree;
    $questionFour = $request->questionFour;
    $request_id = $request->request_id;
    $company_code = $request->company_code;
    $member_id = $request->member_id;
    $request_status = $request->request_status;

    if ($request_id != 0 && $this->checkingIfAlreadyFeedback($request_id)) {
      return response()->json(['status' => false, 'message' => 'You are already send feedback.'], 400);
    }

    if ($request_id != 0 && $this->checkingIfFeedbackLinkIsExpired($request_id)) {
      return response()->json(['status' => false, 'message' => 'Feedback link already expired.'], 400);
    }

    $feedback = Feedback::create([
      'request_id' => $request_id,
      'company_code' => Str::upper($company_code),
      'member_id' => $member_id,
      'request_status' => $request_status,
      'question1' => $questionOne,
      'question2' => $questionTwo,
      'question3' => $questionThree,
      'question4' => $questionFour,
      'comments' => $comment,
    ]);

    return response()->json(['status' => true, 'message' => 'Thank you for sharing your feedback.', 'data' => $feedback]);
  }

  public function checkingIfAlreadyFeedback($request_id)
  {
    $feedback = Feedback::where('request_id', $request_id)->exists();

    return $feedback;
  }

  public function checkingIfFeedbackLinkIsExpired($request_id)
  {
    $feedback = Client::where('id', $request_id)->first();
    // return $feedback;
    return $feedback ? Carbon::parse($feedback->created_at)->diffInDays(now()) >= 3 : null;
  }

  public function exportFeedbackReport(Request $request)
  {
    return (new ClientCareReportExport)->download('feedback-client-care-report.csv');
  }
}
