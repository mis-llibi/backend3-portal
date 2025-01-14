<?php

namespace App\Models\Dental_insurance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class addons extends Model
{
    use HasFactory;

    protected $table = 'dental_insurance_addons';

    protected $fillable = [
        'user_id',
        'desc',
        'quantity',
    ];
}
