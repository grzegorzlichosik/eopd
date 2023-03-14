<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Location extends Model
{
    use HasFactory;
    use UuidTrait;

    protected $fillable = [
        'organisations_id',
        'users_id',
        'name',
        'short_name',
        'address_1',
        'address_2',
        'postcode',
        'city_town',
        'phone',
        'location_lat',
        'location_lon',
        'timezone',
        'files_id',
    ];

    protected $hidden = [
        'id',
        'organisations_id',
        'users_id',
        'files_id'
    ];

    public function organisation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisations_id', 'id');
    }

    public function places(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Place::class, 'locations_id', 'id');
    }

    public function file(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(File::class, 'files_id', 'id');
    }

    public static function getLocations(): array
    {
        return convertToDropdownData(Location::where('organisations_id', Auth::user()->organisations_id)
            ->orderBy('name')
            ->get());
    }

}
