<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompaniesSync extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sync';
    protected $table = 'companies';

    protected $fillable = [
        'benad_password',
    ];
}
