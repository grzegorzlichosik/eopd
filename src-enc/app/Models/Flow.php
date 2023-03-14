<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Threadable\StateMachine\Components\StateableModelInterface;
use Threadable\StateMachine\Components\StateableModelTrait;

class Flow extends Model implements StateableModelInterface
{
    use HasFactory;
    use UuidTrait;
    use StateableModelTrait;

    public const STATE_INITIAL = 'Initial';
    public const STATE_DRAFT = 'Draft';
    public const STATE_PUBLISHED = 'Published';
    public const STATE_INACTIVE = 'Inactive';
    public const STATE_ARCHIVED = 'Archived';

    protected $fillable = [
        'organisations_id',
        'name',
        'objective',
        'json',
        'tsm_current_state',
    ];

    protected $hidden = [
        'id',
        'organisations_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function organisation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisations_id', 'id');
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(User::class, 'flows_users_map', 'flows_id', 'users_id')
            ->withPivot(['pools_id']);
    }

    public function channelUsers(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(User::class, 'channels_users_map', 'flows_id', 'users_id')
            ->withPivot(['channels_id']);
    }

    public function channels(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Channel::class, 'flows_id', 'id');
    }

    public function encounters(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Encounter::class, 'flows_id', 'id');
    }


    public static function getFlows(): array
    {
        return convertToDropdownData(Flow::where('organisations_id', Auth::user()->organisations_id)
            ->orderBy('name')
            ->get());
    }

    public static function flowDetails(string $uuid): Flow
    {
        $flow = Flow::where('organisations_id', auth()->user()->organisations_id)
            ->where('uuid', $uuid)
            ->with(['channels.places', 'users', 'encounters', 'channelUsers'])
            ->with('channels', function ($query) {
                $query->with(['places', 'type:id,label']);
            })
            ->with('channels.type')
            ->first();

        collect($flow->channels)->map(function ($channel) {
            $channel->type_name = trans('channels.' . $channel->type->label);
            return $channel;
        });

        return $flow;
    }

}
