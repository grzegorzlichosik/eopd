<?php

namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\User;
use Tests\TestCase;

class FileTest extends TestCase
{
    public function test_file(): void
    {
        $file = File::factory()->create();
        $this->assertModelExists($file);

        $file = File::factory()->create();
        $file->delete();
        $this->assertModelMissing($file);
    }

    public function test_file_organisation_relation(): void
    {
        $file = File::factory()->create();
        $this->assertModelExists($file);
        $this->assertInstanceOf(Organisation::class, $file->organisation);
        $this->assertEquals($file->organisations_id, $file->organisation->id);
    }

    public function test_file_user_relation(): void
    {
        $file = File::factory()->create();
        $this->assertModelExists($file);
        $this->assertInstanceOf(User::class, $file->user);
        $this->assertEquals($file->users_id, $file->user->id);
    }

    public function test_file_location_relation(): void
    {
        $file = File::factory()->create();
        $location = Location::factory()->create([
           'files_id' => $file->id,
        ]);

        $this->assertModelExists($file);
        $this->assertInstanceOf(Location::class, $file->location);
        $this->assertEquals($file->id, $file->location->files_id);
    }
}
