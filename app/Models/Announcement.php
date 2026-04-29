<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'published_at', 'status', 'user_id', 'scope', 'lib_region_id', 'lib_club_name_id'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'lib_region_id');
    }

    public function club()
    {
        return $this->belongsTo(LibClubName::class, 'lib_club_name_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
