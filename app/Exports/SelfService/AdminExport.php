<?php

namespace App\Exports\SelfService;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Carbon\Carbon;

class AdminExport implements FromArray, WithHeadings, ShouldAutoSize
{
  protected $records;
  public function __construct($records)
  {
    $this->records = $records;
  }

  // public function query()
  // {
  //   $request = new AdminController();
  //   $data = $request->SearchRequest($this->search, $this->status);

  //   return $data;
  // }

  public function headings(): array
  {
    return [
      'MEMBER ID',
      'COMPANY',
      'PATIENT NAME',
      'LOA TYPE',
      'D/T CREATED',
      'APPROVED DATE',
      'APPROVED BY',
      'HANDLING TIME (minutes)',
      'VIBER',
    ];
  }

  // public function map($data): array
  // {
  //   return [
  //     $data->memberID,
  //     $data->lastName . ', ' . $data->firstName,
  //     $data->loaType,
  //     Carbon::parse($data->createdAt),
  //   ];
  // }

  public function array(): array
  {
    $new_records = [];
    foreach ($this->records as $key => $record) {
      $new_records[] = [
        $record->memberID,
        $record->company_name,
        $record->lastName . ', ' . $record->firstName,
        $record->loaType,
        Carbon::parse($record->createdAt),
        $record->approved_date ? Carbon::parse($record->approved_date) : null,
        $record->approved_by_first_name . ', ' . $record->approved_by_last_name,
        $record->elapse_minutes,
        $record->platform == 'viber' ? 'YES' : '-',
      ];
    }

    return $new_records;
  }
}
