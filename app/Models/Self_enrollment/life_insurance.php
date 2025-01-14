<?php

namespace App\Models\Self_enrollment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class life_insurance extends Model
{
    use HasFactory;

    protected $table = 'self_enrollment_life_insurance';

    protected $fillable = [
        'link_id',
        'salary',
        'insurance_no',
        'insurance_encoded_datetime',
        'status'
    ];
}
