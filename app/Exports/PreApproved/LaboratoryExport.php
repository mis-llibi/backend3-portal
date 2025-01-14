<?php

namespace App\Exports\PreApproved;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;

class LaboratoryExport implements FromCollection, WithHeadings, ShouldAutoSize
{
  // very important to run download method from calling this class
  use Exportable;

  protected $data;
  public function __construct($data)
  {
    $this->data = $data;
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
      'CODE',
      'LABORATORY',
      'CLASS_1',
      'CLASS_2',
    ];
  }
}
