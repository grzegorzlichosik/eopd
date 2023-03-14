<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ChannelType extends Model
{
    use HasFactory;
    use UuidTrait;

    public const F2F = 1;
    public const WEB = 2;
    public const PHONE = 3;

    public const TYPES = [
        self::F2F,
        self::WEB,
        self::PHONE,
    ];


    protected $fillable = [
        'name',
        'label'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public static function channelTypes(): array
    {
        return  convertToDropdownData(ChannelType::get()
            ->map(function ($item) {
                return
                    [
                        'name' => trans('channels.' . $item['label']),
                        'uuid' => $item['uuid'],
                    ];
            }));
    }

}
