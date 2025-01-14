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

use Illuminate\Support\Str;

use App\Models\PreApprove\Laboratory;

class LaboratoryImport implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow
{
  /**
   * @param Collection $collection
   */
  public function collection(Collection $rows)
  {
    foreach ($rows as $row) {
      $slug_laboratory = Str::slug(trim($row['description']));

      $is_exist = Laboratory::where('slug_laboratory', $slug_laboratory)->exists();
      try {
        if (!$is_exist) {
          Laboratory::create([
            'code' => Str::random(12),
            'laboratory' => trim($row['description']),
            'slug_laboratory' => $slug_laboratory,
            'cost' => Str::replace(',', '', $row['pre_approved_rates']),
            'cost2' => Str::replace(',', '', $row['pre_approved_rates']),
          ]);
        }
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
