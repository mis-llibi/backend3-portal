<?php

namespace App\Http\Controllers\Feedback;

use App\Exports\Feedback\AteneoReportExport;
use App\Exports\Feedback\ClientCareReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\ManualSendFeedbackRequest;
use App\Models\AdmuFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdmuFeedbackController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $feedbacks = AdmuFeedback::query()
      ->join('app_portal_requests as request', 'request.client_id', '=', 'feedbacks_admu.id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 'feedbacks_admu.member_id')
      ->select('feedbacks_admu.*', 'request.loa_type', 'mlist.company_name')
      ->orderBy('feedbacks_admu.id', 'DESC')->paginate(25);

    $feedback_counts = AdmuFeedback::query()->count();
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
        FROM feedbacks_admu GROUP BY question1)
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
        FROM feedbacks_admu GROUP BY question2)
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
        FROM feedbacks_admu GROUP BY question3)
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
        FROM feedbacks_admu GROUP BY question4)
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

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
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

    $feedback = AdmuFeedback::create([
      'company_code' => 'ADMU',
      'question1' => $questionOne,
      'question2' => $questionTwo,
      'question3' => $questionThree,
      'question4' => $questionFour,
      'comments' => $comment,
      'ip' => $request->ip(),
    ]);

    return response()->json(['status' => true, 'message' => 'Thank you for sharing your feedback.', 'data' => $feedback]);
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

  public function exportFeedbackReport(Request $request)
  {
    return (new AteneoReportExport)->download('feedback-ateneo-report.csv');
  }
}
