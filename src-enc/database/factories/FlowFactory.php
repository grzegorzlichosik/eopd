<?php

namespace Database\Factories;

use App\Models\Flow;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FlowFactory extends Factory
{
    protected $model = Flow::class;

    public function definition()
    {
        return [
            'uuid'              => Str::uuid()->toString(),
            'organisations_id'  => Organisation::factory(),
            'name'              => Str::random(32),
            'objective'         => Str::random(),
            'metadata'          => json_encode(['key' => Str::random()]),
            'tsm_current_state' => Flow::STATE_DRAFT,
        ];
    }
}
