<?php

namespace App\Models\Members;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingDocument extends Model
{
  use HasFactory;

  protected $table = 'pending_documents';

  protected $fillable = [
    'link_id',
    'file_required',
    'file_name',
    'file_link',
  ];

  protected function fileLink(): Attribute
  {
    return Attribute::make(
      get: fn (string | null $value) => $value ? env('DO_CDN_ENDPOINT') . "/" . $value : null,
    );
  }
}
