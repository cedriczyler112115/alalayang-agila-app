<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessTypePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_type_id',
        'module',
        'allow_view',
        'allow_add',
        'allow_edit',
        'allow_delete',
    ];

    protected $casts = [
        'allow_view' => 'boolean',
        'allow_add' => 'boolean',
        'allow_edit' => 'boolean',
        'allow_delete' => 'boolean',
    ];

    public function accessType()
    {
        return $this->belongsTo(AccessType::class);
    }
}
