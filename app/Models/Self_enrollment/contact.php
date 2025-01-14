<?php

namespace App\Models\Self_enrollment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contact extends Model
{
    use HasFactory;

    protected $table = 'self_enrollment_contact';

    protected $fillable = [
        'link_id',
        'street',
        'barangay',
        'city',
        'province',
        'zip_code',
        'email',
        'email2',
        'mobile_no',
    ];

    public function member()
    {
        return $this->belongsTo(members::class, 'link_id');
    }
}
