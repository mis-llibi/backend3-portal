<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_philhealth_correction extends Model
{
    use HasFactory;

    protected $table = 'hr_philhealth_correction';

    protected $fillable = [
        'link_id',
        'philhealth_conditions',
        'position',
        'plan_type',
        'branch_name',
        'philhealth_no',
        'senior_citizen_id_no',
    ];
}
