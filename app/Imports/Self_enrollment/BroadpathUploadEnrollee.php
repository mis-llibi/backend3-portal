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

class BroadpathUploadEnrollee implements ToCollection, WithHeadingRow, WithBatchInserts
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

            $birthDate = $this->changeDateFormat($row['birth_date']);
            $exist = (new ManageBroadpathClients)->checkIfExistingPrincipal($row['employee_id'], $birthDate);
            
            if(!$exist) {

                $rel = strtoupper($row['relation']);

                $members = [
                    'client_company' => $this->comp,
                    'upload_date' => ($rel == 'PRINCIPAL' ? date('Y-m-d') : NULL),
                    'member_id' => strtoupper($row['employee_id']),
                    'hash' => ($rel == 'PRINCIPAL' ? md5($row['employee_id'].date('YmdHis')) : NULL),
                    'mbl' => ($rel == 'PRINCIPAL' ? $row['mbl'] : NULL),
                    'room_and_board' => ($rel == 'PRINCIPAL' ? strtoupper($row['room_and_board']) : NULL),
                    'relation' => $rel,
                    'last_name' => strtoupper($row['last_name']),
                    'first_name' => strtoupper($row['first_name']),
                    'birth_date' => $this->changeDateFormat($row['birth_date']),
                    'gender' => strtoupper($row['gender']),
                    'civil_status' => strtoupper($row['civil_status']),
                    'hire_date' => $this->changeDateFormat($row['start_date']),
                    'coverage_date' => $this->changeDateFormat($row['start_date']),
                    'certificate_no' => strtoupper($row['certificate_no']),
                    'form_locked' => 1,
                    'status' => 4,
                ];

                $member = members::create($members);

                if($rel == 'PRINCIPAL') {
                    $contact = [
                        'link_id' => $member->id,
                        'email'  => trim($row['email_1']),
                        'email2' => trim($row['email_2']),
                        'mobile_no' => $this->clean($row['phone_number']),
                        'street' => strtoupper($row['address']),
                    ];
                    contact::create($contact);
                }

                /* $info = [
                    'hash' => $member->hash,
                    'name' => $member->last_name.', '.$member->first_name,
                    'email' => trim($row['email_1']),
                    'email2' => trim($row['email_2']),
                    'mobile' => $this->clean($row['phone_number'])
                ];
                
                (new ManageBroadpathNotifications)
                    ->invite($info); */

            }
                
        } else {
            return response()->json([
                'message' => 'error'
            ], 402);
        }
    }

    private function clean($string) {
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
