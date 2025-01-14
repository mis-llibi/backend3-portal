<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPortalErrorLogs extends Model
{
  use HasFactory;

  protected $table = 'app_portal_clients_error_logs';

  protected static $unguarded = false;
}
