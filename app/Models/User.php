<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Musonza\Chat\Traits\Messageable;

#[Fillable(['name', 'first_name', 'middle_name', 'last_name', 'extension_name', 'sex', 'birthday', 'marital_status', 'email', 'password', 'google_id', 'status', 'date_approve', 'address', 'location', 'profile_photo', 'eagle_id_card', 'contact_number', 'contact_person_emergency', 'contact_number_emergency', 'lib_region_id', 'lib_club_name_id', 'lib_position_id', 'current_job', 'office', 'make_private', 'access_type_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Messageable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'date_approve' => 'datetime',
        ];
    }

    public function getFullnameAttribute()
    {
        $name = "{$this->last_name}, {$this->first_name} {$this->middle_name}";
        if ($this->extension_name) {
            $name .= " {$this->extension_name}";
        }
        return trim($name);
    }

    public function region()
    {
        return $this->belongsTo(LibRegion::class, 'lib_region_id');
    }

    public function club()
    {
        return $this->belongsTo(LibClubName::class, 'lib_club_name_id');
    }

    public function position()
    {
        return $this->belongsTo(LibPosition::class, 'lib_position_id');
    }

    public function subscriptionPayments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function accessType()
    {
        return $this->belongsTo(AccessType::class, 'access_type_id');
    }

    public function hasPermission($module, $action = 'view')
    {
        if ($this->is_admin) {
            return true;
        }

        if (!$this->access_type_id || !$this->accessType) {
            return false;
        }

        $permission = $this->accessType->permissions()->where('module', $module)->first();

        if (!$permission) {
            return false;
        }

        switch ($action) {
            case 'view':
                return $permission->allow_view;
            case 'add':
                return $permission->allow_add;
            case 'edit':
                return $permission->allow_edit;
            case 'delete':
                return $permission->allow_delete;
            default:
                return false;
        }
    }
}
