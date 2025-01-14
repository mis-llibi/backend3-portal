<?php

namespace App\Models\Self_service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
  use HasFactory;

  protected $table = 'complaint';

  protected $fillable = [
    'uuid',
    'last_name',
    'first_name',
    'middle_name',
    'dob',
    'email',
    'ercard_no',
    'company_name',

    'deps_last_name',
    'deps_first_name',
    'deps_dob',
    'deps_ercard_no',
  ];
}
