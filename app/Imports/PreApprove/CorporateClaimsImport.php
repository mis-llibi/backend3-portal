<?php

namespace App\Imports\PreApprove;

use App\Models\PreApprove\Claims;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpParser\Node\Stmt\TryCatch;

class CorporateClaimsImport implements ToCollection, WithBatchInserts, WithChunkReading
{
  public function collection(Collection  $rows)
  {
    $i = 0;
    foreach ($rows as $row) {
      // skipping heading or row 1
      if ($i > 0) {
        $dateString = $row[6];
        $date  = Carbon::createFromFormat('d/m/Y', $dateString);
        try {
          $isExist = Claims::where([
            'uniqcode' => $row[0], 'empcode' => $row[1], 'claimnumb' => $row[2], 'seriesnumb' => $row[3]
          ])->exists();

          if (!$isExist) {
            Claims::insert([
              'uniqcode' => $row[0],
              'empcode' => $row[1],
              'claimnumb' => $row[2],
              'seriesnumb' => $row[3],
              'compcode' => $row[4],
              'claimtype' => $row[5],
              'claimdate' => $date->format('Y-m-d'),
              'diagcode' => $row[7],
              'diagname' => $row[8],
              'eligible' => $row[9],
            ]);
          }
        } catch (\Throwable $th) {
          Log::error($th);
          throw $th;
        }
      }
      $i++;
    }
  }

  public function batchSize(): int
  {
    return 1000;
  }

  public function chunkSize(): int
  {
    return 1000;
  }
}
