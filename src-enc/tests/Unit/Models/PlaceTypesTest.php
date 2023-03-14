<?php

namespace Tests\Unit\Models;

use App\Models\PlaceType;
use Tests\TestCase;

class PlaceTypesTest extends TestCase
{
    public function test_place_types(): void
    {
        $placeType = PlaceType::find(PlaceType::RESOURCED);
        $this->assertModelExists($placeType);

        $placeType = PlaceType::find(PlaceType::MANAGED);
        $this->assertModelExists($placeType);

        $placeType = PlaceType::find(PlaceType::OPEN);
        $this->assertModelExists($placeType);

    }

}
