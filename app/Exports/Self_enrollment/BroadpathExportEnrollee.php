<?php

namespace App\Exports\Self_enrollment;

use DB;

use App\Models\Self_enrollment\members;
use App\Models\Self_enrollment\contact;
use App\Models\Self_enrollment\attachment;

use App\Http\Controllers\Self_enrollment\ManageBroadpathEnrollee;
use App\Http\Controllers\Self_enrollment\ManageSelfEnrollmentController;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;

use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BroadpathExportEnrollee implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithStyles
{
    public function  __construct($comp)
    {
        $this->comp = $comp;
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => [
                'font' => [
                    'bold' => true
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'E1E1E1'
                    ],
                ],
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'EMPID/OID',
            'REMARKS',
            'LAST NAME',
            'FIRST NAME',
            'MIDDLE NAME',
            'RELATION',
            'CIVIL STATUS',
            'GENDER',
            'BIRTHDAY',
            'HIRED DATE',
            'MBL',
            'ROOM & BOARD',
            'EST_PREMIUM',
            'ADDRESS',
            'MOBILE',
            'EMAIL',
            'EMAIL PERSONAL',
            'CERTIFICATE #'
        ];
    }
    
    public function registerEvents(): array
    {
        $arr = '2, 4';
        $result = (new ManageSelfEnrollmentController)->getAllClients($arr, $this->comp);

        $sum = 0;
        foreach ($result['list'] as $key => $row) {
            $sum += (int)$row->attachments;
        }

        $count = count($result['list']) + 1 + $sum;

        return [

            AfterSheet::class => function (AfterSheet $event) use ($count) {

                //Birth Certificate
                foreach ($event->sheet->getColumnIterator('A') as $row) {

                    foreach ($row->getCellIterator() as $attach) {

                        if ($attach->getValue() != "" && str_contains($attach->getValue(), 'Self_enrollment/')) {

                            $attach->setHyperlink(new Hyperlink(url('../storage/'.$attach->getValue()), 'View attached file'));
                            $attach->setValue('View File');

                            $event->sheet->getStyle($attach->getCoordinate())->applyFromArray([
                                'font' => [
                                    'color' => ['rgb' => '0000FF'],
                                    'underline' => 'single'
                                ]
                            ])
                            ->getAlignment()
                            ->setWrapText(true);

                        }

                    }

                }

                //Check if attachment available
                foreach ($event->sheet->getColumnIterator('B') as $row) {

                    foreach ($row->getCellIterator() as $cert) {
                        if (str_contains($cert->getValue(), '* Attachment/s is in next row')) {
                            $event->sheet->getStyle($cert->getCoordinate())->applyFromArray([
                                'font' => [
                                    'color' => ['rgb' => 'DF0A0A'],
                                    'bold' => true
                                ]
                            ])
                            ->getAlignment()
                            ->setWrapText(true);
                        }
                    }
                }

                $event->sheet->getStyle('A1:R'.$count)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

            },

        ];
    }
    
    public function array(): array
    {
        $arr = '2, 4';
        $result = (new ManageSelfEnrollmentController)->getAllClients($arr, $this->comp);

        $rs = array(); $memId = ''; $mbl = ''; $dep = 0; $rnb = '';
        foreach($result['list'] as $row) 
        {
            
            if($row->relation == 'PRINCIPAL') {
                $mbl = number_format($row->mbl);
                $rnb = strtoupper($row->room_and_board);
                $premComp = 'N/A';

                $dep = 1;
            } 
            
            switch ($mbl) {
                case '200,000':
                    $premium = 19807.20;
                    break;
                case '150,000':
                    $premium = 19398.40;
                    break;
                default:
                    $premium = 0;
                    break;
            }

            if($row->relation != 'PRINCIPAL') {
                $premComp = $premium * 0.2;
                if($dep > 2)
                    $premComp = $premium * 1;

                $premComp = number_format($premComp, 2);

                $dep++;
            }

            $rs[] = array(
                $row->member_id,
                (int)$row->attachments == 1 ? 
                '* Attachment/s is in next row' : '',
                str_replace('ñ', 'Ñ', strtoupper($row->last_name)),
                str_replace('ñ', 'Ñ', strtoupper($row->first_name)),
                str_replace('ñ', 'Ñ', strtoupper($row->middle_name)),
                strtoupper($row->relation),
                strtoupper($row->civil_status),
                strtoupper($row->gender),
                strtoupper($row->birth_date),
                strtoupper($row->hire_date),
                $mbl,
                $rnb,
                $premComp,
                (!empty($this->address($row->id)) ? $this->address($row->id)['address'] : ''),
                (!empty($this->address($row->id)) ? $this->address($row->id)['mobile'] : ''),
                (!empty($this->address($row->id)) ? $this->address($row->id)['email'] : ''),
                (!empty($this->address($row->id)) ? $this->address($row->id)['email2'] : ''),
                $row->certificate_no
            );

            $att = attachment::where('link_id', $row->id)
                ->get(['file_link']);   
                if((int)$row->attachments) {
                    $ps = ['', ''];
                    foreach ($att as $key1 => $row1) {
                        $ps[] = $row1->file_link;
                    }
                    $rs[] = $ps;
                }
            
        }
        return [$rs]; 
    }

    public function address($id)
    {
        $address = contact::where('link_id', $id)
            ->limit(1)
            ->get();

        if(count($address) > 0) {
            $add = ""; $mobile = "";
            foreach ($address as $key => $row) {
                $add = $row->street.", ".$row->barangay.", ".$row->city.", ".$row->province.", ".$row->zip_code;
                $mobile = $row->mobile_no;
                $email = $row->email;
                $email2 = $row->email2;
            }
            return array('address' => $add, 'mobile' => $mobile, 'email' => $email, 'email2' => $email2);
        }
        return;
    }

    public function attachments($id)
    {
        $attachments = attachment::where('link_id', $id)
            ->get(['file_link']);

        if(count($attachments) > 0) {
            $tr = [];
            foreach ($attachments as $key => $row) {
                $tr[] = $row->file_link;
            }
           return implode("\n", $tr);
        }
        return;
    }
}
