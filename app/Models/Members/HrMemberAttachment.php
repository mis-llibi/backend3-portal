<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrMemberAttachment extends Model
{
  use HasFactory;

  protected $table = 'hr_member_attachments';

  protected $fillable = [
    'link_id',
    'file_name',
    'file_link',
  ];

  protected function fileLink(): Attribute
  {
    return Attribute::make(
      get: fn (string $value) => env('DO_CDN_ENDPOINT') . "/" . $value,
    );
  }
}
