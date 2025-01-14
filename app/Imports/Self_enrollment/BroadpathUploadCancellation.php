<?php

namespace App\Imports\Self_enrollment;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use App\Models\Self_enrollment\members;
use App\Models\Self_enrollment\contact;
use App\Models\Self_enrollment\attachment;

use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Self_enrollment\ManageBroadpathNotifications;

class BroadpathUploadCancellation implements ToCollection, WithHeadingRow, WithBatchInserts
{
    /**
    * @param Collection $collection
    */
    public function __construct($comp)
    {
        $this->comp = $comp;
    }

    public function collection(Collection $rows)
    {
        foreach($rows as $row) 
        {
            $this->importToMasterlist($row);
        }
    }

    private function importToMasterlist($row)
    {
        if(trim(!empty($row['employee_id']))) {

            $members = [
                'status' => 3,
                'form_locked' => 1
            ];

            $member = members::where('member_id', $row['employee_id'])
                ->where('birth_date', $this->changeDateFormat($row['birth_date']))
                ->where('client_company', $this->comp)
                ->update($members);
                    
        } else {
            return response()->json([
                'message' => 'error'
            ], 402);
        }
    }

    private function clean($string)
    {
        $string = str_replace('-', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/\s+/', '', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    private function changeDateFormat($oldDate)
    {
        $dateYear = date('Y', strtotime('-1 year'));
        $date = $dateYear.'-01-01';

        if(strlen(str_replace(" ", "", $oldDate)) == 5) {
            $unix_date = ($oldDate - 25569) * 86400;
            $date = gmdate('Y-m-d', $unix_date);
        } else if(strlen(trim($oldDate)) >= 10) {
            $date = date('Y-m-d', strtotime($oldDate));
        }

        return $date;
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
