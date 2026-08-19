<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibRegion extends Model
{
    protected $table = 'lib_region';
    protected $fillable = ['name', 'logo', 'notification_keyword'];

    public function clubs()
    {
        return $this->hasMany(LibClubName::class, 'lib_region_id');
    }
}
