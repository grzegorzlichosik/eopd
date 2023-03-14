<?php

namespace Database\Seeders;

use App\Models\ChannelType;
use Illuminate\Database\Seeder;

class ChannelTypesSeeder extends Seeder
{
    public function run()
    {
        foreach (self::getTypes() as $type) {
            $placeType = ChannelType::where('name', $type['name'])->first();
            if (!$placeType) {
                ChannelType::create($type);
            }
        }
    }

    private static function getTypes(): array
    {
        return [
            [
                'id'    => ChannelType::F2F,
                'name'  => 'Face to Face',
                'label' => 'face_to_face'
            ],
            [
                'id'    => ChannelType::WEB,
                'name'  => 'Web',
                'label' => 'web'
            ],
            [
                'id'    => ChannelType::PHONE,
                'name'  => 'Phone',
                'label' => 'phone'
            ],
        ];
    }
}
