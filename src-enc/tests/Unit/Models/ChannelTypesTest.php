<?php

namespace Tests\Unit\Models;

use App\Models\ChannelType;
use Tests\TestCase;

class ChannelTypesTest extends TestCase
{
    public function test_channel_types(): void
    {
        $ChannelType = ChannelType::find(ChannelType::F2F);
        $this->assertModelExists($ChannelType);

        $ChannelType = ChannelType::find(ChannelType::WEB);
        $this->assertModelExists($ChannelType);

        $ChannelType = ChannelType::find(ChannelType::PHONE);
        $this->assertModelExists($ChannelType);

    }

}
