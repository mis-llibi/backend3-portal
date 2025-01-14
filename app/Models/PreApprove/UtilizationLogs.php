<?php

namespace App\Models\PreApprove;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilizationLogs extends Model
{
  use HasFactory;

  protected $connection = 'mysql_pre_approve';
  protected $table = 'utilization_logs';

  protected $fillable = ['pre_approved_log_id', 'member_id', 'diagname', 'amount'];
}
