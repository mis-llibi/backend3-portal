<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Members\hr_members;

class hr_members_correction extends Model
{
  use HasFactory;

  protected $table = 'hr_members_correction';

  protected $fillable = [
    'client_company',
    'member_id',
    'member_link_id',
    'employee_no',
    'hash',
    'first_name',
    'middle_name',
    'last_name',
    'extension',
    'gender',
    'member_type',
    'birth_date',
    'relationship_id',
    'civil_status',
    'effective_date',
    'date_hired',
    'reg_date',
    'if_enrollee_is_a_philhealth_member',
    'client_remarks',
    'admin_remarks',
    'status',
    'late_enrolled_remarks',
    'changed_status_at',
    'attachments',
    'created_by',
    'excel_batch',
    'pending_submission_created_at',
    'pending_deleted_at',
    'plan',
    'action_code',
    'certificate_issued_at',
    'change_plan_at',
    'deleted_remarks',
    'approved_deleted_member_at',
    'is_approved',
    'pending_correction_at',
    'approved_correction_at',
  ];

  public function member(): BelongsTo
  {
    return $this->belongsTo(hr_members::class, 'id', 'member_link_id');
  }
}
