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
use App\Http\Controllers\Self_enrollment\ManageBroadpathClients;

use DB;

class BroadpathUploadForApproving implements ToCollection, WithHeadingRow, WithBatchInserts
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
        // Find the last index of each client
        $lastIndex = array();
        foreach ($rows as $index => $row) {
            $empno = $row['employeeno'];
            $lastIndex[$empno] = $index;
        }

        foreach($rows as $index => $row) 
        {
            $this->importToMasterlist($row, $index, $lastIndex);
        }
    }

    private function importToMasterlist($row, $index, $lastIndex)
    {
        if(trim(!empty($row['employeeno']))) {

            // Logging the data for debugging
            \Log::info('Updating member:', [
                'member_id' => $row['employeeno'],
                'birth_date' => $row['dateofbirth'],
                'client_company' => $this->comp,
                'certificate_no' => $row['certificateno'],
                'certificate_encode_datetime' => date('Y-m-d H:i:s'),
                'status' => 5,
            ]);

            $cmembers = [
                'certificate_no' => $row['certificateno'],
                'certificate_encode_datetime' => date('Y-m-d H:i:s'),
                'status' => 5,
            ];

            $updateResult = DB::table('self_enrollment_members')
                ->where('member_id', $row['employeeno'])
                ->where('birth_date', $this->changeDateFormat($row['dateofbirth']))
                ->where('client_company', $this->comp)
                ->update($cmembers);
                
            \Log::info('Update result:', ['result' => $updateResult]);

            if ($index === $lastIndex[$row['employeeno']])
                (new ManageBroadpathClients)->getDetailsOfApprovedClient($row['employeeno']);
                    
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
