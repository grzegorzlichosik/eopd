<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\PlaceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlaceTypesSeeder extends Seeder
{
    public function run()
    {
        foreach (self::getTypes() as $type) {
            $placeType = PlaceType::where('name', $type['name'])->first();
            if (!$placeType) {
                PlaceType::create($type);
            }
        }
    }

    private static function getTypes(): array
    {
        return [
            [
                'id'    => 1,
                'name'  => 'Resourced',
                'label' => 'resourced'
            ],
            [
                'id'    => 2,
                'name'  => 'Managed',
                'label' => 'managed'
            ],
            [
                'id'    => 3,
                'name'  => 'Open',
                'label' => 'open'
            ],
        ];
    }
}
