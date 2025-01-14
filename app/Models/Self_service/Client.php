<?php

namespace App\Models\Self_service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Self_service\ClientRequest;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
  use HasFactory;

  protected $table = 'app_portal_clients';

  protected $fillable = [

    'request_type',
    'reference_number',

    'email',
    'alt_email',
    'contact',

    'member_id',
    'first_name',
    'last_name',
    'dob',

    'is_dependent',
    'dependent_member_id',
    'dependent_first_name',
    'dependent_last_name',
    'dependent_dob',

    'status',
    'user_id',
    'approved_date',
    'is_sent',

    'created_at',
    'provider_email2',
    'is_send_to_provider',
    'handling_time',
    'platform',

    'opt_landline',
    'callback_remarks',
    'landline',
    'opt_contact'
  ];

  /**
   * Get the user that owns the Client
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function clientRequest(): HasOne
  {
    return $this->HasOne(ClientRequest::class, 'client_id', 'id');
  }
}
