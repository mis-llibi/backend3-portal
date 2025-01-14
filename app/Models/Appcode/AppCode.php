<?php

namespace App\Models\Appcode;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppCode extends Model
{
    use HasFactory;

    protected $connection = 'mysql_appcode';
    protected $table = 'codes';

    protected $fillable = [
        'loatype',
        'approval_code',
        'loa_number',
        'employee_name',
        'patient_name',
        'datetime',
    ];
}
