<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;
    use UuidTrait;

    protected $fillable = [
        'organisations_id',
        'flows_id',
        'channel_types_id',
        'max_participants',
        'is_auto_confirm',
        'is_default',
    ];

    protected $hidden = [
        'id',
        'organisations_id',
        'flows_id',
        'channel_types_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_auto_confirm' => 'boolean',
        'is_default'      => 'boolean',
    ];

    public function organisation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisations_id', 'id');
    }

    public function flow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Flow::class, 'flows_id', 'id');
    }

    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChannelType::class, 'channel_types_id', 'id');
    }

    public function places(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(Place::class, 'channels_places_map', 'channels_id', 'places_id');
    }

    public function encounters(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Encounter::class, 'channels_id', 'id');
    }

    public function channelUsers(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(User::class, 'channels_users_map', 'channels_id', 'users_id');
    }

    public static function getChannels()
    {
        $channels = request()->input('channel') ?
            ChannelType::where('uuid', request()->input('channel'))->first() : '';
        if ($channels) {
            $channels->name = trans('channels.' . $channels->label);
        }
        return $channels;
    }


}
