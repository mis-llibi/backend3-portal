<?php

namespace App\Imports\Deel;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use Maatwebsite\Excel\Concerns\WithChunkReading;

class DepsImport implements ToCollection, WithBatchInserts, WithChunkReading
{
  /**
   * @param Collection $collection
   */
  public function collection(Collection $rows)
  {
    $i = 0;
    foreach ($rows as $row) {
      $empID = $row[0];
      $bday = $row[1];

      // skipping heading or row 1
      if ($i > 0) {
        $dateString = $row[1];
        $startDateString = $row[11];

        $birthDate = Carbon::createFromFormat('d/m/Y', $dateString);
        $startDate = Carbon::createFromFormat('d/m/Y', $startDateString);

        try {
          // Log::info([$birthDate->format('Y-m-d'), $startDate->format('Y-m-d'), $row]);
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
