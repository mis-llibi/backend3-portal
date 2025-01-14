<?php

namespace App\Models\Self_enrollment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attachment extends Model
{
    use HasFactory;

    protected $table = 'self_enrollment_attachment';

    protected $fillable = [
        'link_id',
        'file_name',
        'file_link',
    ];
}
