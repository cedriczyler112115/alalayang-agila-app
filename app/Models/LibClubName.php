<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibClubName extends Model
{
    protected $table = 'lib_club_name';
    protected $fillable = ['name', 'lib_region_id', 'logo'];

    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'lib_region_id');
    }
}
