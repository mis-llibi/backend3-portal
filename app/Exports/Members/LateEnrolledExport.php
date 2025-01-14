<?php

namespace App\Exports\Members;

use App\Models\Members\hr_members;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LateEnrolledExport implements FromCollection, WithHeadings, ShouldAutoSize
{
  public function __construct(protected $data)
  {
  }
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return $this->data;
  }

  public function headings(): array
  {
    return [
      'EmpNo',
      'LastName',
      'FirstName',
      'MiddleName',
      'Extension',
      'Gender',
      'Street',
      'City',
      'Province',
      'ZipCode',
      'Email',
      'MobileNumber',
      'MemberType',
      'BirthDate',
      'RelationshipId',
      'CivilStat',
      'EffectiveDate',
      'DateHired',
      'RegDate',
      'IfEnrolleeIsAPhilhealthMember',
      'PhilhealthConditions',
      'Position',
      'PlanType',
      'BranchName',
      'PhilhealthNumber',
      'SeniorCitizenIdNumber',
      'ClientRemarks',
      'LateEnrolledRemarks',
    ];
  }
}
