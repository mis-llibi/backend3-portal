<?php

namespace App\Imports\PreApprove;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Models\PreApprove\Utilization;

class UtilizationImport implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow
{
  public function collection(Collection  $rows)
  {
    $i = 0;
    foreach ($rows as $row) {
      if ($i == 0) {
        Utilization::where('compcode', $row['compcode'])->delete();
        $i++;
      }

      $dateString = $row['claimdate'];
      $date  = Carbon::createFromFormat('m/d/Y', $dateString);

      try {
        Utilization::insert([
          'uniqcode' => trim($row['uniqcode']),
          'empcode' => trim($row['empcode']),
          'claimnumb' => trim($row['claimnumb']),
          'seriesnumb' => trim($row['seriesnumb']),
          'compcode' => trim($row['compcode']),
          'claimtype' => trim($row['claimtype']),
          'claimdate' => $date->format('Y-m-d'),
          'diagcode' => trim($row['diagcode']),
          'diagname' => trim($row['diagname']),
          'eligible' => trim($row['eligible']),
          // 'relation' => trim($row['insrela']),
        ]);
      } catch (\Throwable $th) {
        Log::error($th->getMessage());
        // throw $th;
      }
    }
  }

  public function batchSize(): int
  {
    return 10000;
  }

  public function chunkSize(): int
  {
    return 10000;
  }
}
