<?php

namespace App\Models\Self_service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'app_portal_attachment';

    protected $fillable = [
        'request_id',
        'file_name',
        'file_link',
    ];
}
