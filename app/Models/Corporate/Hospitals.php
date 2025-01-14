<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospitals extends Model
{
    use HasFactory;

    // protected $connection = 'mysql_corporate';
    protected $connection = 'mysql_sync';
    protected $table = 'hospitals';
}
