<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSetting extends Model
{
  use HasFactory;

  protected $table = 'settings';
  protected $fillable = ['minutes', 'receiver', 'receiver_email'];

  protected function receiver(): Attribute
  {
    return Attribute::make(
      get: fn (string $value) => substr($value, 1, 10),
    );
  }
}
