<?php

namespace App\Models\Self_enrollment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class members extends Model
{
  use HasFactory;

  protected $table = 'self_enrollment_members';

  protected $fillable = [
    'client_company',
    'vendor',
    'upload_date',
    'is_renewal',
    'plan',
    'mbl',
    'room_and_board',
    'member_id',
    'hash',
    'relation',
    'first_name',
    'last_name',
    'middle_name',
    'birth_date',
    'gender',
    'civil_status',
    'skip_hierarchy',
    'skip_reason',
    'skip_document',
    'hire_date',
    'end_date',
    'coverage_date',
    'certificate_no',
    'certificate_encode_datetime',
    'kyc',
    'kyc_timestamp',
    'with_er_card',
    'milestone',
    'form_locked',
    'status',
  ];

  public function contact()
  {
    return $this->hasOne(contact::class, 'link_id');
  }
}
