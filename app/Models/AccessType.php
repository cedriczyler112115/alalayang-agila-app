<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function permissions()
    {
        return $this->hasMany(AccessTypePermission::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
