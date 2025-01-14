<?php

namespace App\Models\Self_service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    use HasFactory;

    protected $table = 'app_portal_requests';

    protected $fillable = [
        'client_id',
        'member_id',

        'provider_id',
        'provider',
        'provider_updated_email',
        'doctor_id',
        'doctor_name',
        
        'loa_type',
        'complaint',
        'assessment_q1',
        'assessment_q2',
        'assessment_q3',
        'diagnosis',
        
        'lab_attachment',
        'loa_number',
        'approval_code',

        'status',
    ];
}
