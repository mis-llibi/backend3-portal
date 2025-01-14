<?php

namespace App\Models\Company_policies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_policies extends Model
{
    use HasFactory;

    protected $table = 'company_policies';
    protected $fillable = [
        'company_id',
        'policy_name',
        'endorsement_name'
    ];
}
