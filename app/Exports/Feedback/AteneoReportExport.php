<?php

namespace App\Exports\Feedback;

use App\Models\AdmuFeedback;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use App\Models\FeedbackCorporate;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Support\Str;

class AteneoReportExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings
{
  use Exportable;
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $feedbacks = AdmuFeedback::query()
      ->join('app_portal_requests as request', 'request.client_id', '=', 'feedbacks_admu.id')
      ->leftJoin('llibiapp_sync.masterlist as mlist', 'mlist.member_id', '=', 'feedbacks_admu.member_id')
      ->select(
        'feedbacks_admu.comments',
        'feedbacks_admu.question1',
        'feedbacks_admu.question2',
        'feedbacks_admu.question3',
        'feedbacks_admu.question4',
        'request.loa_type',
        'mlist.company_name'
      )
      ->orderBy('feedbacks_admu.id', 'DESC')->get();

    return $feedbacks;
  }

  public function map($feedbacks): array
  {
    return [
      'ADMU',
      Str::upper($feedbacks->loa_type),
      $feedbacks->comments,
      $feedbacks->question1,
      $feedbacks->question2,
      $feedbacks->question3,
      $feedbacks->question4,
      Carbon::parse($feedbacks->created_at)->format('Y-m-d'),
    ];
  }

  public function headings(): array
  {
    return [
      'COMPANY',
      'LOA_TYPE',
      'COMMENTS',
      'QUESTION_1',
      'QUESTION_2',
      'QUESTION_3',
      'QUESTION_4',
      'DATE_CREATED',
    ];
  }
}
