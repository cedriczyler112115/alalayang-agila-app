<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibTelegram extends Model
{
    protected $table = 'lib_telegram';
    public $timestamps = false;

    protected $fillable = [
        'club_id',
        'token',
        'link',
        'group_id',
        't_group_name',
    ];

    public function club()
    {
        return $this->belongsTo(LibClubName::class, 'club_id');
    }
}
