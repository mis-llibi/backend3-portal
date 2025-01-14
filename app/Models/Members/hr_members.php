<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Enums\Broadpath\Members\StatusEnum;
use Illuminate\Support\Facades\Log;

class hr_members extends Model
{
  use HasFactory;

  protected $table = 'hr_members';

  protected $fillable = [
    'client_company',
    'member_id',
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
    'certificate_no',
    'certificate_issued_at',
    'change_plan_at',
    'deleted_remarks',
    'approved_deleted_member_at',
    'approved_by',
    'approved_member_at',
    'approved_change_plan_at',
    'nationality',
    'approved_deleted_member_at_original',
    'pending_correction_at',
    'approved_correction_at',
  ];

  // protected $guarded = [];

  protected $appends = ['status_name', 'full_name'];

  /* 
    * #####################
    * # LEGENDS OF STATUS #
    * #####################

    * 1 pending submission
    * 3 Pending deletion
    * 5 pending correction/edit information
    * 8 pending change plan

    * all this status is considered as active
    * 4 approved/active members
    * 6 approved correction/edit information
    * 7 approved deletion
    * 9 approved change plan
    * 10 disapproved member
  */

  protected function middleName(): Attribute
  {
    return Attribute::make(
      get: fn (string $value) => $value ?? '',
      set: fn (string $value) => $value ?? ''
    );
  }

  protected function fullName(): Attribute
  {
    return Attribute::make(
      get: fn () => $this->last_name . ', ' . $this->first_name . ' ' . $this->middle_name,
    );
  }

  protected function statusName(): Attribute
  {
    return Attribute::make(
      get: function () {
        return match ($this->status) {
          // pending
          StatusEnum::PENDING_MEMBER->value => 'Pending Member',
          StatusEnum::PENDING_DELETION->value => 'Pending Deletion',
          StatusEnum::PENDING_CORRECTION->value => 'Pending Correction',
          StatusEnum::PENDING_CHANGE_PLAN->value => 'Pending Change Plan',

          // approve or active
          StatusEnum::ACTIVE_MEMBER->value => 'Approved Member',
          StatusEnum::APPROVED_CORRECTION->value => 'Approved Correction',
          StatusEnum::APPROVED_DELETION->value => 'Approved Deletion',
          StatusEnum::APPROVED_CHANGE_PLAN->value => 'Approved Change Plan',
          StatusEnum::DISAPPROVED_MEMBER->value => 'Disapproved Member',
          StatusEnum::PENDING_DOCUMENTS->value => 'Pending Documents',
          default => '',
        };
      },
    );
  }

  public function scopePendingSubmission(Builder $query): void
  {
    $query->where('status', StatusEnum::PENDING_MEMBER->value);
  }

  // public function scopeSumittedMembers(Builder $query): void
  // {
  //   $query->where('status', 2);
  // }

  public function scopePendingDeletion(Builder $query): void
  {
    $query->where('status', StatusEnum::PENDING_DELETION->value);
  }

  public function scopeApprovedMembers(Builder $query): void
  {
    // $query->where('status', 4);
    $query->whereNotNull('approved_member_at')->where('status', '<>', StatusEnum::APPROVED_DELETION->value);
  }

  public function scopePendingCorrection(Builder $query): void
  {
    $query->where('status', StatusEnum::PENDING_CORRECTION->value);
  }

  public function scopeApprovedCorrection(Builder $query): void
  {
    $query->where('status', StatusEnum::APPROVED_CORRECTION->value);
  }

  public function scopeDeletedMember(Builder $query): void
  {
    $query->where('status', StatusEnum::APPROVED_DELETION->value);
  }

  public function scopePendingChangePlan(Builder $query): void
  {
    $query->where('status', StatusEnum::PENDING_CHANGE_PLAN->value);
  }

  public function scopeApprovedChangePlan(Builder $query): void
  {
    $query->where('status', StatusEnum::APPROVED_CHANGE_PLAN->value);
  }

  public function scopeActiveMembers(Builder $query): void
  {
    /**
     * 4 approved/active members
     * 6 approved correction
     * 7 approved deletion
     * 9 approved change plan
     * 10 disapproved member
     */
    $query->whereIn('status', [
      StatusEnum::ACTIVE_MEMBER->value,
      StatusEnum::APPROVED_CORRECTION->value,
      StatusEnum::APPROVED_DELETION->value,
      StatusEnum::APPROVED_CHANGE_PLAN->value,
      StatusEnum::DISAPPROVED_MEMBER->value
    ]);
  }

  public function scopePendingApproval(Builder $query): void
  {
    /**
     * 1 pending submission
     * 3 Pending deletion
     * 5 pending correction
     * 8 pending change plan
     * 11 pending documents
     */
    $query->whereIn('status', [
      StatusEnum::PENDING_MEMBER->value,
      StatusEnum::PENDING_DELETION->value,
      StatusEnum::PENDING_CORRECTION->value,
      StatusEnum::PENDING_CHANGE_PLAN->value,
      StatusEnum::PENDING_DOCUMENTS->value
    ]);
  }

  public function scopeActiveMembersWithPending(Builder $query): void
  {
    /**
     * 3 Pending deletion
     * 5 pending correction
     * 8 pending change plan
     */
    $query->whereIn('status', [
      StatusEnum::PENDING_DELETION->value,
      StatusEnum::PENDING_CORRECTION->value,
      StatusEnum::PENDING_CHANGE_PLAN->value,
    ]);
  }

  public function scopePrincipal(Builder $query): void
  {
    $query->where('relationship_id', 'PRINCIPAL');
  }

  public function scopeDisapprovedMember(Builder $query): void
  {
    $query->where('status', StatusEnum::DISAPPROVED_MEMBER->value);
  }

  public function scopePendingDocuments(Builder $query): void
  {
    $query->where('status', StatusEnum::PENDING_DOCUMENTS->value);
  }


  // ELOQUENT RELATIONSHIP
  public function changePlanPending(): HasOne
  {
    return $this->hasOne(HrMemberChangePlanCorrection::class, 'member_link_id', 'id')->orderByDesc('id');
  }

  public function contact(): HasOne
  {
    return $this->hasOne(hr_contact::class, 'link_id', 'id');
  }

  public function correction(): HasOne
  {
    return $this->hasOne(hr_members_correction::class, 'member_link_id', 'id')->orderByDesc('id');
  }
  public function contactCorrection(): HasOne
  {
    return $this->hasOne(hr_contact_correction::class, 'link_id', 'id')->orderByDesc('id');
  }
}
