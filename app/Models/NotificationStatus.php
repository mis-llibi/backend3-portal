<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationStatus extends Model
{
    use HasFactory;

    protected $table = 'notification_status';

    protected $fillable = [
        'app',
        'client_id',
        'client_company',
        'status',
        'date',
    ];
}