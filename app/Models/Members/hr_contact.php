<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_contact extends Model
{
    use HasFactory;

    protected $table = 'hr_contact';

    protected $fillable = [
        'link_id',
        'barangay',
        'street',
        'city',
        'province',
        'zip_code',
        'email',
        'mobile_no',
    ];
}
