<?php

namespace Tests\Feature\Platform;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Tests\TestCase;

class NylasLinkTest extends TestCase
{
    protected const EMAIL = 'testuser1@whencounter.us';

    public function test_get_callback_without_code_nylas_gsuite()
    {
        $platformOrganisation = Organisation::where('is_platform', 1)->first();

        $admin = User::factory()->twoFactorConfirmed()
            ->create(['organisations_id' => $platformOrganisation->id]);


        $organisation = Organisation::factory()->create([
            'master_calendar_nylas_access_token' => Str::random(),
            'master_calendar_nylas_account_id'   => Str::random(),
        ]);
        Session::put('organisationUuid', $organisation->uuid);
        $this->withoutMiddleware()->actingAs($admin)->get('/master/oauth/callback', [
            'code' => ''
        ]);

        $response = $this->actingAs($admin)->get(route('platform.organisations.retry'));
        $response->assertSessionHas('organisationUuid');
        $this->assertDatabaseHas('organisations', [
            'uuid'                                      => $organisation->uuid,
            'master_calendar_nylas_access_token'        => null,
            'master_calendar_nylas_account_id'          => null,
        ]);

        $response = $this->actingAs($admin)->get('/platform/organisations/'. $organisation->uuid .'/master_nylas');
        $response->assertStatus(302);
    }

}
