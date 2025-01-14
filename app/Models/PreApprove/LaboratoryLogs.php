<?php

namespace App\Models\PreApprove;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryLogs extends Model
{
  use HasFactory;

  protected $connection = 'mysql_pre_approve';
  protected $table = 'laboratory_logs';

  protected $fillable = ['pre_approved_log_id', 'member_id', 'laboratory', 'amount'];
}
