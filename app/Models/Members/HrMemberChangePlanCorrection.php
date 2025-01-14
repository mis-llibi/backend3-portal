<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrMemberChangePlanCorrection extends Model
{
  use HasFactory;

  protected $table = 'hr_members_change_plan_correction';

  protected $fillable = [
    'member_link_id',
    'plan',
    'created_by',
  ];
}
