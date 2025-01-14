<?php

namespace App\Models\Self_service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncPartnership extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sync';
    protected $table = 'partnership';
    
    public function sync()
    {
        return $this->belongsTo(Sync::class, 'member_id', 'member_id');
    }

}
