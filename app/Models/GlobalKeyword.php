<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalKeyword extends Model
{
    protected $table = 'global_keyword';
    public $timestamps = false;

    protected $fillable = ['desc', 'keyword', 'agila_help', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
