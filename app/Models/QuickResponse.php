<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickResponse extends Model
{
    protected $table = 'quick_response';
    protected $fillable = ['user_id', 'lib_help_id', 'details', 'location'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libHelp()
    {
        return $this->belongsTo(LibHelp::class, 'lib_help_id');
    }
}
