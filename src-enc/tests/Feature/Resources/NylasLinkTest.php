<?php

namespace Tests\Feature\Resources;

use App\Models\Organisation;
use App\Models\User;
use Tests\TestCase;

class NylasLinkTest extends TestCase
{
    protected const EMAIL = 'testuser1@tf4cg.onmicrosoft.com';

    public function test_link_room_calendar_ms_page_redirected()
    {
        $authUser = User::factory()->admin()->twoFactorConfirmed()->create([
            'email'=> self::EMAIL
        ]);
        $redirectUri =   urlencode(env('APP_URL') . '/resources/oauth/callback');
        $response = $this->actingAs($authUser)->get(route('resources.init'));
        $this->assertEquals(
            "https://login.windows.net/common/oauth2/v2.0/authorize?client_id=" . env('MS_CLIENT_ID') . "&redirect_uri=" . $redirectUri . "&login_hint=testuser1%40tf4cg.onmicrosoft.com&response_type=code&scope=offline_access+OnlineMeetings.ReadWrite+openid+Place.Read.All+profile+User.Read+EAS.AccessAsUser.All+EWS.AccessAsUser.All",
            $response->getTargetUrl()
        );
    }

    public function test_get_callback_with_code_link_room_calendar()
    {
        $organisation = Organisation::factory()->create();
        $authUser = User::factory()->superAdmin()->create([
            'email' => self::EMAIL,
            'organisations_id' => $organisation->id
        ]);

        $this->withoutMiddleware()->actingAs($authUser)->get(route('calendar.oauth.callback', [
            'code' => ''
        ]));

        $this->actingAs($authUser)->get(route('resources.retry'));
        $this->assertDatabaseHas('organisations', [
            'id'                        => $authUser->organisations_id,
            'nylas_access_token'        => null,
            'nylas_account_id'          => null,
            'nylas_provider'            => null,
            'microsoft_refresh_token'   => null
        ]);

        $response = $this->actingAs($authUser)->get(route('resources.init'));
        $response->assertStatus(302);
    }



}
