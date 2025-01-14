<?php

namespace App\Models\PreApprove;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
  use HasFactory;

  protected $connection = 'mysql_pre_approve';
  protected $table = 'laboratory';

  protected $fillable = ['code', 'laboratory', 'slug_laboratory', 'cost', 'cost2'];

  // public function getCostAttribute($value)
  // {
  //   return number_format($value, 2);
  // }
}
