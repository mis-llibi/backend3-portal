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


class PhilcareMemberExport implements FromCollection, WithEvents, WithHeadings, WithHeadingRow, WithCustomStartCell, ShouldAutoSize, WithColumnWidths
{
  use Exportable;

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $new_members = [];
    $members = hr_members::query()->where('status', '2')->get();

    foreach ($members as $key => $row) {
      $principal = $row['relationship_id'] !== 'PRINCIPAL' ? hr_members::query()->where('member_id', $row['member_id'])->where('relationship_id', 'PRINCIPAL')->first() : '';

      $data = [
        'sub_office_name' => '',
        'employee_number' => $row['member_id'],
        'last_name' => $row['last_name'],
        'first_name' => $row['first_name'],
        'middle_name' => $row['middle_name'],
        'principal' => $principal ? $principal->last_name . ", " . $principal->first_name : "",
        'bmonth' => $row['birth_date'] ? Carbon::parse($row['birth_date'])->format('M') : '',
        'bday' => $row['birth_date'] ? Carbon::parse($row['birth_date'])->format('j') : '',
        'byear' => $row['birth_date'] ? Carbon::parse($row['birth_date'])->format('Y') : '',
        'sex' => $row['gender'],
        'nationality' => '',
      ];

      array_push($new_members, $data);
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

        // $sheet->setCellValue('A7', 'Please make the following changes in your record of employees and/or dependent of:');
        // $sheet->setCellValue('A9', 'NAME OF COMPANY:');
        // $sheet->setCellValue('A10', 'Agreement No.');
        // $sheet->setCellValue('A14', 'MEMBERSHIP UPDATE FORM (MUF)');

        // HEADER
        $sheet->setCellValue('A1', 'SubOffice Name');
        $sheet->setCellValue('B1', 'Employee Number');
        $sheet->setCellValue('C1', 'Enrollee\'s Name');
        $sheet->setCellValue('F1', 'Name of Principal (Lastname,FIRSTNAME)');
        $sheet->setCellValue('G1', 'BIRTHDATE');
        $sheet->setCellValue('J1', 'Sex');
        $sheet->setCellValue('K1', 'Nationality');

        /**
         * set merging cells
         */
        $sheet->mergeCells('C1:E1');
        $sheet->mergeCells('G1:I1');

        /** 
         * set bg to yellow
         */
        $cellRange = 'A1:AF1';
        $sheet->getStyle($cellRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');

        /** 
         * back to bg white
         */
        $sheet->getStyle('L1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
        $sheet->getStyle('V1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
        $sheet->getStyle('AB1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');

        /**
         * set font styles
         */
        $sheet->getStyle($cellRange)->getFont()->setBold(true);
        $sheet->getStyle($cellRange)->getAlignment()->setHorizontal('center')->setVertical('center');

        /**
         * set row height base on specific row
         */
        $sheet->getRowDimension('1')->setRowHeight(40);
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
    return [
      'G' => 10,
      'H' => 10,
      'I' => 10,
    ];
  }

  public function startCell(): string
  {
    return 'A2';
  }
}
