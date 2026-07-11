<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'phone', 'phone_verified', 'status', 'otp_code', 'otp_expires_at', 'client_organization_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
        ];
    }

    /**
     * Get the client organization associated with this user.
     */
    public function clientOrganization()
    {
        return $this->belongsTo(\App\Modules\CRM\Models\ClientOrganization::class, 'client_organization_id');
    }

    /**
     * Get the panelist profile associated with this user.
     */
    public function panelistProfile()
    {
        return $this->hasOne(\App\Modules\Wallet\Models\PanelistProfile::class);
    }

    /**
     * Get the transactions log for this user.
     */
    public function transactions()
    {
        return $this->hasMany(\App\Modules\Wallet\Models\Transaction::class);
    }
}
