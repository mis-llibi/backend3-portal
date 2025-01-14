<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_contact_correction extends Model
{
    use HasFactory;

    protected $table = 'hr_contact_correction';

    protected $fillable = [
        'link_id',
        'street',
        'city',
        'province',
        'zip_code',
        'email',
        'mobile_no',
    ];
}
