<?php

namespace App\Models\Self_service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientErrorLog extends Model
{
  use HasFactory;

  protected $table = 'app_portal_clients_error_logs';

  protected $fillable = [

    'request_type',
    'reference_number',

    'email',
    'alt_email',
    'contact',

    'member_id',
    'first_name',
    'last_name',
    'dob',

    'is_dependent',
    'dependent_member_id',
    'dependent_first_name',
    'dependent_last_name',
    'dependent_dob',

    'status',
    'user_id',
    'approved_date',
    'is_sent',

    'created_at',
    'provider_email2',
    'is_send_to_provider',
    'handling_time',
    'platform',
  ];
}
