<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletionAttachment extends Model
{
  use HasFactory;

  protected $table = 'hr_member_deletion_attachment';

  protected $guarded = [];

  protected function fileLink(): Attribute
  {
    return Attribute::make(
      get: fn (string $value) => env('DO_CDN_ENDPOINT') . "/" . $value,
    );
  }
}
