<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Threadable\StateMachine\Components\StateableModelInterface;
use Threadable\StateMachine\Components\StateableModelTrait;

class Encounter extends Model implements StateableModelInterface
{
    use HasFactory;
    use UuidTrait;
    use StateableModelTrait;

    public const STATE_INITIAL = 'Initial';
    public const STATE_SCHEDULED = 'Scheduled';
    public const STATE_CANCELLED = 'Cancelled';
    public const STATE_PENDING_AUTO_RE_SCHEDULING = 'Pending Auto Re-scheduling';
    public const STATE_PENDING_MANUAL_RE_SCHEDULING = 'Pending Manual Re-scheduling';
    public const STATE_FINISHED = 'Finished';

    protected $dates = [
        'scheduled_at',
        'ends_at',
    ];

    protected $fillable = [
        'organisations_id',
        'flows_id',
        'channels_id',
        'channel_types_id',
        'agent_id',
        'places_id',
        'external_id',
        'tsm_current_state',
        'metadata',
        'scheduled_at',
        'ends_at',
        'duration'
    ];

    protected $hidden = [
        'id',
        'organisations_id',
        'flows_id',
        'channels_id',
        'channel_types_id',
        'agent_id',
        'places_id',
    ];

    protected $appends = [
        'duration',
    ];

    public function organisation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisations_id', 'id');
    }

    public function agent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }

    public function flow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Flow::class, 'flows_id', 'id');
    }

    public function channel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channels_id', 'id');
    }

    public function channel_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChannelType::class, 'channel_types_id', 'id');
    }

    public function place(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Place::class, 'places_id', 'id');
    }

    public function attendees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EncounterAttendee::class, 'encounters_id', 'id');
    }

    protected function duration(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->ends_at && $this->scheduled_at
                ? $this->ends_at->diffForHumans($this->scheduled_at)
                : null,
        );
    }
}
