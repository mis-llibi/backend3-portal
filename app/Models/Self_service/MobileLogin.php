<?php

namespace App\Models\Self_service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;   
use Laravel\Sanctum\HasApiTokens;

class MobileLogin extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $connection = 'mysql_sync';
    protected $table = 'masterlist';

    protected $fillable = [
        'member_id',
        'last_name',
        'first_name',
        'middle_name',
        'relation',
        'birth_date',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'birth_date',
    ];
}
