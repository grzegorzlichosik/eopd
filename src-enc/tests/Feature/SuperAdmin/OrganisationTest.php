<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Organisation;
use App\Models\User;
use http\Header;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Faker;

class OrganisationTest extends TestCase
{
    use WithFaker;

    private User $user;
    private Organisation $organisation;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepare_test_data();
    }

    public function test_can_update(): void
    {
        $fileName = 'sample';
        $fileType = 'png';
        $fullFileName = $fileName . '.' . $fileType;
        $fakeImage = UploadedFile::fake()->image($fullFileName);
        $mimeType = 'image/png';
        $base64image = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($fakeImage->path()));

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post(route('organisation.update'),
                [
                    'name'          => Str::random(),
                    'phone_number'  => '+91 9876543210',
                    'country_code'  => 'IN',
                    'dial_code'     => '91',
                    'color'         => 'F00000',
                    'file'          => $base64image,
                    'file_name'     => $fileName,
                    'file_type'     => $fileType,
                    'file_mimetype' => $mimeType
                ]
            );

        $this->assertDatabaseHas(
            'files',
            [
                'name' => $fullFileName
            ]
        );
        $response->assertSessionHas('toaster');
        $this->assertEquals(
            session('toaster')['message'],
            trans('modals.organisation_updated')
        );
        $response->assertStatus(302);
    }

    public function test_can_view_logo(): void
    {
        $fileName = 'sample';
        $fileType = 'png';
        $fullFileName = $fileName . '.' . $fileType;
        $fakeImage = UploadedFile::fake()->image($fullFileName);
        $mimeType = 'image/png';
        $base64image = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($fakeImage->path()));

        $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post(route('organisation.update'),
                [
                    'name'          => Str::random(),
                    'phone_number'  => '+91 9876543210',
                    'country_code'  => 'IN',
                    'dial_code'     => '91',
                    'color'         => 'F00000',
                    'file'          => $base64image,
                    'file_name'     => $fileName,
                    'file_type'     => $fileType,
                    'file_mimetype' => $mimeType
                ]
            );

        $response = $this->actingAs($this->user)->get(route('organisation.logo'));

        $response->assertOk();
        $responseType = $response->headers->get('content-type');
        $this->assertEquals('image/png',$responseType);
    }

    public function test_can_view_empty_logo(): void
    {
        $fileName = 'sample';
        $fileType = 'png';
        $fullFileName = $fileName . '.' . $fileType;
        $fakeImage = UploadedFile::fake()->image($fullFileName);
        $mimeType = 'image/png';
        $base64image = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($fakeImage->path()));

        $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post(route('organisation.update'),
                [
                    'name'          => Str::random(),
                    'phone_number'  => '+91 9876543210',
                    'country_code'  => 'IN',
                    'dial_code'     => '91',
                    'color'         => 'F00000',
                    'file'          => $base64image,
                    'file_name'     => $fileName,
                    'file_type'     => $fileType,
                    'file_mimetype' => $mimeType
                ]
            );

        $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete(route('organisation.logo.delete'));

        $response = $this->actingAs($this->user)
            ->get(route('organisation.logo'));

        $response->assertOk();
        $responseType = $response->headers->get('content-type');
        $this->assertStringContainsString('text/html', $responseType);
    }

    public function test_can_view_organisation_details(): void
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->get(route('organisation.show'));
        $response->assertStatus(200);
    }

    public function test_can_delete_logo(): void
    {
        $fileName = 'sample';
        $fileType = 'png';
        $fullFileName = $fileName . '.' . $fileType;
        $fakeImage = UploadedFile::fake()->image($fullFileName);
        $mimeType = 'image/png';
        $base64image = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($fakeImage->path()));

        $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post(route('organisation.update'),
                [
                    'name'          => Str::random(),
                    'phone_number'  => '+91 9876543210',
                    'country_code'  => 'IN',
                    'dial_code'     => '91',
                    'color'         => 'F00000',
                    'file'          => $base64image,
                    'file_name'     => $fileName,
                    'file_type'     => $fileType,
                    'file_mimetype' => $mimeType
                ]
            );

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete(route('organisation.logo.delete'));
        $response->assertSessionHas('toaster');
        $this->assertEquals(
            session('toaster')['message'],
            trans('modals.logo_deleted')
        );
        $response->assertStatus(302);

    }

    public function test_cannot_delete_logo(): void
    {
        $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete(route('organisation.logo.delete'));

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete(route('organisation.logo.delete'));

        $response->assertStatus(302);
        $response->assertSessionHas('toaster');
        $this->assertEquals('error', session('toaster')['type']);
    }

    private function prepare_test_data(): void
    {
        $this->organisation = Organisation::factory()
            ->create();
        $this->user = User::factory()
            ->superAdmin()
            ->create(
                [
                    'organisations_id' => $this->organisation->id,
                ]
            );
        $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post(route('organisation.update'),
                [
                    'name'         => Str::random(),
                    'phone_number' => '+91 9876543210',
                    'country_code' => 'IN',
                    'dial_code'    => '91',
                    'color'        => '000000',
                    'file'         => UploadedFile::fake()->image('sample-jpg.jpg'),
                ]
            );
    }
}
