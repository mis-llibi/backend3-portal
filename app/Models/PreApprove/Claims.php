<?php

namespace App\Models\PreApprove;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claims extends Model
{
  use HasFactory;

  protected $connection = 'mysql_claims';
  protected $table = 'claims';

  protected $fillable = ['uniqcode', 'empcode', 'claimnumb', 'seriesnumb', 'compcode', 'claimtype', 'claimdate', 'diagcode', 'diagname', 'eligible'];

  public $timestamps = false;
}
