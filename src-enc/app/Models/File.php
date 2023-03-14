<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    use UuidTrait;

    protected $fillable = [
        'organisations_id',
        'users_id',
        'place_types_id',
        'name',
        'mimetype',
        'size',
        'locations_id',
    ];

    protected $hidden = [
        'id',
        'organisations_id',
        'users_id',
        'place_types_id',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function organisation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisations_id', 'id');
    }

    public function location(): \Illuminate\Database\Eloquent\Relations\hasOne
    {
        return $this->hasOne(Location::class, 'files_id', 'id');
    }
}
