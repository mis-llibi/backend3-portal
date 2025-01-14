<?php

namespace App\Models\PreApprove;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreApprovedLogs extends Model
{
  use HasFactory;

  protected $connection = 'mysql_pre_approve';
  protected $table = 'pre_approved_logs';

  protected $fillable = ['member_id', 'company', 'plan_type', 'mbl', 'reservation', 'utilization', 'laboratory'];
}
