<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Healthdash extends Model
{
  use HasFactory;

  protected $connection = 'mysql_healthdash';
  protected $table = 'login_details';
}
