<?php

namespace App\Imports\Self_enrollment;

use App\Http\Controllers\Self_enrollment\PreqinNotificationController;
use App\Models\Self_enrollment\contact;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use Illuminate\Support\Str;

// models
use App\Models\Self_enrollment\members;
use Illuminate\Support\Facades\Log;

class PreqinImportPrincipal implements ToCollection, WithHeadingRow, WithBatchInserts
{
  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    foreach ($collection as $key => $row) {
      $members = [
        'client_company' => 'PREQIN',
        'upload_date' => date('Y-m-d'),
        'member_id' => strtoupper($row['member_id']),
        'hash' => Str::uuid(),
        // 'mbl' => $row['mbl'],
        // 'room_and_board' => strtoupper($row['room_and_board']),
        'relation' => 'PRINCIPAL',
        'last_name' => strtoupper($row['last_name']),
        'first_name' => strtoupper($row['first_name']),
        'birth_date' => Carbon::createFromFormat('d/m/Y', $row['birth_date'])->format('Y-m-d'),
        'gender' => strtoupper($row['gender']),
        // 'civil_status' => strtoupper($row['civil_status']),
        'hire_date' => Carbon::createFromFormat('d/m/Y', $row['hire_date'])->format('Y-m-d'),
        'coverage_date' => Carbon::createFromFormat('d/m/Y', $row['coverage_date'])->format('Y-m-d'),
        'status' => 1,
      ];

      $member = members::create($members);

      $contact = [
        'link_id' => $member->id,
        'email'  => trim($row['email_1']),
        'email2' => trim($row['email_2']),
        // 'mobile_no' => Str::substr($row['phone_number'], -1, 9),
      ];
      contact::create($contact);

      $info = [
        'hash' => $member->hash,
        'name' => $member->last_name . ', ' . $member->first_name,
        'email' => trim($row['email_1']),
        'email2' => trim($row['email_2']),
        // 'mobile' => Str::substr($row['phone_number'], -1, 9)
      ];

      // Log::info($row);

      (new PreqinNotificationController)->invite($info);
    }
  }

  public function headingRow(): int
  {
    return 1;
  }

  public function batchSize(): int
  {
    return 1000;
  }
}
