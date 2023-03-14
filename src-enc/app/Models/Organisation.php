<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    use HasFactory;
    use UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'address',
        'dial_code',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'files_id',
        'nylas_access_token',
        'microsoft_refresh_token',
        'linked_by',
        'master_calendar_password',
        'master_calendar_userkey',
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(User::class, 'organisations_id', 'id');
    }

    public function pools(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Pool::class, 'organisations_id', 'id');
    }

    public function created_by(): \Illuminate\Database\Eloquent\Relations\hasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Location::class, 'organisations_id', 'id');
    }

    public function places(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Place::class, 'organisations_id', 'id');
    }

    public function flows(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Flow::class, 'organisations_id', 'id');
    }

    public function channels(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Channel::class, 'organisations_id', 'id');
    }

    public function masterCalendarPassword(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_null($value) ? null : decrypt($value),
            set: fn($value) => is_null($value) ? null : encrypt($value),
        );
    }
}
