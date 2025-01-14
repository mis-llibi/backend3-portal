<?php

namespace App\Imports\Members;

use App\Models\Members\hr_members;
use App\Models\Members\hr_contact;
use App\Models\Members\hr_philhealth;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use Illuminate\Support\Facades\Validator;

class MasterlistImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
  protected $late_enrolled = [];

  /**
   * @param Collection $collection
   */
  public function  __construct(public $comp)
  {
    $this->comp = $comp;
  }

  public function collection(Collection $rows)
  {
    foreach ($rows as $row) {
      $this->importToMasterlist($row);
    }
  }

  private function importToMasterlist($row)
  {
    if (!empty($row['empno'])) {
      $is_exists = hr_members::where('employee_no', $row['empno'])->exists();
      if (!$is_exists) {
        $date_hired = $this->changeDateFormat($row['datehired']);
        $members = [
          'employee_no' => $row['empno'],
          'last_name' => $row['lastname'],
          'first_name' => $row['firstname'],
          'middle_name' => $row['middlename'],
          'extension' => $row['extension'],
          'gender' => $row['gender'],
          'member_type' => $row['membertype'],
          'birth_date' => $this->changeDateFormat($row['birthdate']),
          'relationship_id' => $row['relationshipid'],
          'civil_status' => $row['civilstat'],
          'effective_date' => $this->changeDateFormat($row['effectivedate']),
          'date_hired' => $date_hired,
          'reg_date' =>  $this->changeDateFormat($row['regdate']),
          'if_enrollee_is_a_philhealth_member' => $row['if_enrollee_is_a_philhealth_member'],
          'client_remarks' => $row['client_remarks'],
          'status' => (Carbon::parse($date_hired)->diffInDays(now()) >= 31 ? 101 : 1),
        ];
        $member = hr_members::create($members);

        if ($member->status == 101) {
          array_push($this->late_enrolled, $member);
        }

        $contact = [
          'link_id' => $member->id,
          'street' => $row['street'],
          'city' => $row['city'],
          'province' => $row['province'],
          'zip_code' => $row['zipcode'],
          'email' => $row['email'],
          'mobile_no' => $row['mobileno'],
        ];
        hr_contact::create($contact);

        $philHealth = [
          'link_id' => $member->id,
          'philhealth_conditions' => $row['philhealth_conditions'],
          'position' => $row['position'],
          'plan_type' => $row['plan_type'],
          'branch_name' => $row['branch_name'],
          'philhealth_no' => $row['philhealth_no'],
          'senior_citizen_id_no' => $row['senior_citizen_id_no'],
        ];
        hr_philhealth::create($philHealth);
      }
    }
  }

  private function changeDateFormat($oldDate)
  {
    $dateYear = date('Y', strtotime('-1 year'));
    $date = $dateYear . '-01-01';

    if (strlen(str_replace(" ", "", $oldDate)) == 5) {
      $unix_date = ($oldDate - 25569) * 86400;
      $date = gmdate('Y-m-d', $unix_date);
    } else if (strlen(trim($oldDate)) >= 10) {
      $date = date('Y-m-d', strtotime($oldDate));
    }

    return $date;
  }

  public function headingRow(): int
  {
    return 2;
  }

  public function batchSize(): int
  {
    return 1000;
  }

  public function getLateEnrolledImport()
  {
    return $this->late_enrolled;
  }
}
