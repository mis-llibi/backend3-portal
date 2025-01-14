<?php

namespace App\Models\PreApprove;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilization extends Model
{
  use HasFactory;

  protected $connection = 'mysql_pre_approve';
  protected $table = 'utilization';

  protected $fillable = ['uniqcode', 'empcode', 'claimnumb', 'seriesnumb', 'compcode', 'claimtype', 'claimdate', 'diagcode', 'diagname', 'eligible', 'relation'];

  public $timestamps = false;
}
