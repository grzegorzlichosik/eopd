<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Threadable\StateMachine\Components\StateableModelInterface;
use Threadable\StateMachine\Components\StateableModelTrait;

class Place extends Model implements StateableModelInterface
{
    use HasFactory;
    use UuidTrait;
    use StateableModelTrait;

    public const STATE_INITIAL = 'Initial';
    public const STATE_INACTIVE = 'Inactive';
    public const STATE_ACTIVE = 'Active';
    public const STATE_ARCHIVED = 'Archived';
    public const STATE_ERROR = 'Error';


    protected $fillable = [
        'organisations_id',
        'locations_id',
        'place_types_id',
        'name',
        'external_id',
        'email',
        'description',
        'is_active',
        'metadata',
    ];

    protected $hidden = [
        'id',
        'organisations_id',
        'locations_id',
        'place_types_id',
        'pivot',
    ];

    protected $casts =[
        'metadata' => 'array',
    ];

    public function organisation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisations_id', 'id');
    }

    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class, 'locations_id', 'id');
    }

    public function place_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PlaceType::class, 'place_types_id', 'id');
    }

    public function encounters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Encounter::class, 'places_id', 'id');
    }

    public function channels(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(Channel::class, 'channels_places_map', 'places_id', 'channels_id');
    }

    public static function getPlaces(): array
    {
        return convertToDropdownData(Place::where('organisations_id', Auth::user()->organisations_id)
            ->orderBy('name')
            ->get());
    }
}
