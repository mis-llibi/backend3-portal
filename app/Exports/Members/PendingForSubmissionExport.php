<?php

namespace App\Exports\Members;

use App\Models\Members\hr_members;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use App\Enums\Broadpath\Members\RelationEnum;
use App\Enums\Broadpath\Members\StatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PendingForSubmissionExport implements FromCollection, WithEvents, WithHeadings, WithHeadingRow, WithCustomStartCell, ShouldAutoSize, WithColumnWidths
{
  private $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $new_members = [];

    if (!empty($this->data['members'])) {
      foreach ($this->data['members'] as $key => $row) {
        $principal = $row['relationship_id'] !== Str::upper(RelationEnum::EMPLOYEE->value)
          ? hr_members::query()->where('member_id', $row['member_id'])->principal()->first()
          : '';


        $address = '';
        $street = $row->contact['street'] ? $row->contact['street'] . ', ' : '';
        $barangay  = $row->contact['barangay'] ? $row->contact['barangay'] . ', ' : '';
        $city = $row->contact['city'] ? $row->contact['city'] . ', ' : '';
        $province = $row->contact['province'] ? $row->contact['province'] . ', ' : '';
        $zip_code = $row->contact['zip_code'] ? $row->contact['zip_code'] : '';

        $address .= $street . $barangay . $city . $province . $zip_code;

        $data = [
          'sub_office_name' => '',
          'employee_number' => $row['member_id'],
          'last_name' => $row['last_name'],
          'first_name' => $row['first_name'],
          'middle_name' => $row['middle_name'],
          'principal' => $principal ? $principal->last_name . ", " . $principal->first_name : "",

          'birth_date' => $row['birth_date'] ? Carbon::parse($row['birth_date'])->format('m/d/Y') : '',

          'sex' => $row['gender'],
          'nationality' => '',
          'pccupation_job_position_rank' => '',
          'desired_plan' => $row['status'] == StatusEnum::PENDING_CHANGE_PLAN->value ? $row->changePlanPending['plan'] : '',
          'civil_status' => $row['civil_status'],
          'e_mail_address' => $row->contact['email'],

          'hiring_date' => $row['date_hired'] ? Carbon::parse($row['date_hired'])->format('m/d/Y') : '',

          'regularization_date' => $row['reg_date'] ? Carbon::parse($row['reg_date'])->format('m/d/Y') : '',

          'dental_code' => '',
          'desired_action_code' => $this->getActionCode($row['status'], $row['relationship_id']),
          'relationship_code' => $this->getRelationshipCode($row['relationship_id']),

          'effective_date' => $row['effective_date'] ? Carbon::parse($row['effective_date'])->format('m/d/Y') : '',

          'remarks' => '',
          'mobile_number' => $row->contact['mobile_no'],
          'address' => $address,
        ];

        array_push($new_members, $data);
      }
    }



    return collect($new_members);
  }

  // public function drawings()
  // {
  /**
   * add image in excel
   */
  // $drawing = new Drawing();
  // $drawing->setName('Philcare Logo');
  // $drawing->setPath(Storage::path('public/philcare/images/philcarelogo.png'));
  // $drawing->setHeight(90);
  // $drawing->setCoordinates('A1');

  // return $drawing;
  // }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $sheet = $event->sheet->getDelegate();

        // HEADER
        $sheet->setCellValue('A1', Str::upper('SubOffice Name'));
        $sheet->setCellValue('B1', Str::upper('Employee Number'));

        $sheet->setCellValue('C1', Str::upper('Last Name'));
        $sheet->setCellValue('D1', Str::upper('First Name'));
        $sheet->setCellValue('E1', Str::upper('Middle Initial'));
        // $sheet->mergeCells('C1:E1');

        $sheet->setCellValue('F1', Str::upper('Name of Principal (Lastname,FIRSTNAME)'));
        $sheet->setCellValue('G1', Str::upper('Birthdate'));
        $sheet->setCellValue('H1', Str::upper('Sex'));
        $sheet->setCellValue('I1', Str::upper('Nationality'));
        $sheet->setCellValue('J1', Str::upper('Occupation/Job Position/Rank'));
        $sheet->setCellValue('K1', Str::upper('Desired Plan'));
        $sheet->setCellValue('L1', Str::upper('Civil Status'));
        $sheet->setCellValue('M1', Str::upper('E-mail Address'));
        $sheet->setCellValue('N1', Str::upper('Hiring Date'));
        $sheet->setCellValue('O1', Str::upper('Regularization Date'));
        $sheet->setCellValue('P1', Str::upper('Dental Code'));
        $sheet->setCellValue('Q1', Str::upper('Desired Action Code'));
        $sheet->setCellValue('R1', Str::upper('Relationship Code'));
        $sheet->setCellValue('S1', Str::upper('Effective Date'));
        $sheet->setCellValue('T1', Str::upper('Remarks'));
        $sheet->setCellValue('U1', Str::upper('Mobile Number'));
        $sheet->setCellValue('V1', Str::upper('Address'));
        // $sheet->mergeCells('V1:X1');

        /** 
         * set bg to yellow
         */
        $cellRange = 'A1:Z1';
        // $sheet->getStyle($cellRange)
        //   ->getFill()
        //   ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        //   ->getStartColor()
        //   ->setARGB('FFFF00');

        /** 
         * back to bg white
         */
        // $sheet->getStyle('J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
        // $sheet->getStyle('P1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
        // $sheet->getStyle('T1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');

        /**
         * set font styles
         */
        $sheet->getStyle($cellRange)->getFont()->setBold(true);
        $sheet->getStyle($cellRange)->getAlignment()->setHorizontal('center')->setVertical('center');

        /**
         * set row height base on specific row
         */
        // $sheet->getRowDimension('1')->setRowHeight(40);
      },
    ];
  }

  // public function styles(Worksheet $sheet)
  // {
  //   $sheet->getStyle('A16')->getFont()->setBold(true);
  // }

  public function headings(): array
  {
    return [];
  }

  public function columnWidths(): array
  {
    return [];
  }

  public function startCell(): string
  {
    return 'A2';
  }

  private function getRelationshipCode($relationship_id)
  {
    return match ($relationship_id) {
      'PRINCIPAL' => '0',
      'SPOUSE' => '1',
      'CHILD' => '2',
      'PARENT' => '3',
      'SIBLING' => '4',
      default => '5',
    };
  }

  private function getActionCode($status, $relationship_id)
  {
    $action_code = '';
    if ($status == StatusEnum::PENDING_MEMBER->value) {
      $action_code = match (Str::upper($relationship_id)) {
        // Additional Principal (New Employee/Member)
        Str::upper(RelationEnum::EMPLOYEE->value) => 'A',
        // Additional Dependent Siblings(Brother/Sister)
        Str::upper(RelationEnum::SIBLING->value) => 'ADSib',
        // Additional Dependent Spouse
        Str::upper(RelationEnum::SPOUSE->value) => 'ADS',
        // Additional Dependent Child
        Str::upper(RelationEnum::CHILD->value) => 'ADC',
        // Additional Dependent Parent
        Str::upper(RelationEnum::PARENT->value) => 'ADP',
        default => ''
      };
    } else {
      $action_code = match ($status) {
        // Upgrading of Plan
        StatusEnum::PENDING_CHANGE_PLAN->value => 'UPG',
        // Changes in Personal Data (i.e. name, status, etc.)
        StatusEnum::PENDING_CORRECTION->value => 'CHP',
        // Cancelled Principal (Terminated Member)
        StatusEnum::PENDING_DELETION->value => 'C',
        default => ''
      };
    }

    return $action_code;
  }
}
