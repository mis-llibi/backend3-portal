<?php

namespace App\Models\Self_service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpiredLogs extends Model
{
    use HasFactory;

    protected $table = 'expired_logs';

    protected $fillable = [
        'member_id',
        'company_code',
        'first_name',
        'last_name',
        'incepfrom',
        'incepto',
        'birth_date',
    ];
}
