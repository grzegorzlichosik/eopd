<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use App\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use UuidTrait;
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'organisations_id',
        'password',
        'password_updated_at',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'is_super_admin',
        'is_admin',
        'is_agent',
        'is_developer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'organisations_id',
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'nylas_access_token',
        'microsoft_refresh_token',
        'pivot',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'           => 'datetime',
        'two_factor_confirmed_at'     => 'datetime',
        'password_updated_at'         => 'datetime',
        'locked_at'                   => 'datetime',
        'two_factor_reset_request_at' => 'datetime',
        'is_admin'                    => 'boolean',
        'is_super_admin'              => 'boolean',
        'is_agent'                    => 'boolean',
        'is_developer'                => 'boolean',
    ];

    public function organisation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisations_id', 'id');
    }

    public function latestLogin(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UsersLatestLogin::class, 'users_id', 'id')
            ->orderBy('created_at', 'DESC')
            ->skip(1)
            ->take(1);
    }

    public function pools(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(Pool::class, 'pools_users_map', 'users_id', 'pools_id');
    }

    public function flows(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(Flow::class, 'flows_users_map', 'users_id', 'flows_id');
    }

    public function channels(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(Channel::class, 'channels_users_map', 'users_id', 'channels_id')
            ->withPivot('flows_id');
    }

    public function encounters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Encounter::class, 'agent_id', 'id');
    }

    public function setTwoFactorResetRequest()
    {
        $this->password = Str::random(12);
        $this->password_updated_at = now();
        $this->two_factor_recovery_codes = null;
        $this->two_factor_secret = null;
        $this->two_factor_confirmed_at = null;
        $this->two_factor_reset_request_at = now();
        $this->failed_2fa_counter = 0;
        $this->failed_logins = 0;
        $this->save();
    }

    public function resetTwoFactorResetRequest()
    {
        $this->two_factor_reset_request_at = null;
        $this->save();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function scopeSuperAdministrator(Builder $query): Builder
    {
        return $query->where('is_super_admin', 1);
    }

    public function scopeAdministrator(Builder $query): Builder
    {
        return $query->where('is_admin', 1);
    }

    public function scopeAgent(Builder $query): Builder
    {
        return $query->where('is_agent', 1);
    }

    public function scopeDeveloper(Builder $query): Builder
    {
        return $query->where('is_developer', 1);
    }

    public static function getAgents(): array
    {
        return convertToDropdownData(User::where('organisations_id', Auth::user()->organisations_id)
            ->where('is_agent', 1)
            ->orderBy('name')
            ->get());
    }

}
