<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceType extends Model
{
    use HasFactory;
    use UuidTrait;

    public const RESOURCED = 1;
    public const MANAGED = 2;
    public const OPEN = 3;

    public const TYPES = [
        self::RESOURCED,
        self::MANAGED,
        self::OPEN,
    ];


    protected $fillable = [
        'name',
        'label'
    ];

    protected $hidden = [
        'id',
    ];
}
