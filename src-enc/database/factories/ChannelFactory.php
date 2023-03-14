<?php

namespace Database\Factories;

use App\Models\ChannelType;
use App\Models\Flow;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Channel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    public function definition()
    {
        return [
            'organisations_id' => Organisation::factory(),
            'flows_id'         => Flow::factory(),
            'channel_types_id' => ChannelType::F2F,
            'max_participants' => 1,
            'is_auto_confirm'  => Arr::random([0, 1]),
            'is_default'       => Arr::random([0, 1]),
        ];
    }

    public function face2face(): ChannelFactory
    {
        return $this->state(function () {
            return [
                'channel_types_id' => ChannelType::F2F,
            ];
        });
    }

    public function phone(): ChannelFactory
    {
        return $this->state(function () {
            return [
                'channel_types_id' => ChannelType::PHONE,
            ];
        });
    }

    public function web(): ChannelFactory
    {
        return $this->state(function () {
            return [
                'channel_types_id' => ChannelType::WEB,
            ];
        });
    }
}
