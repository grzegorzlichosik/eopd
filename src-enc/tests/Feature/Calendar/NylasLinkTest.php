<?php

namespace Tests\Feature\Calendar;

use App\Models\User;
use Tests\TestCase;

class NylasLinkTest extends TestCase
{
    protected const EMAIL = 'testuser1@tf4cg.onmicrosoft.com';

    public function test_link_my_calendar_ms_page_redirected()
    {
        $authUser = User::factory()->admin()->twoFactorConfirmed()->create([
            'email' => self::EMAIL
        ]);
        $redirectUri =   urlencode(env('APP_URL') . '/calendar/oauth/callback');
        $response = $this->actingAs($authUser)->get(route('calendar.init'));

        $this->assertEquals(
            "https://login.windows.net/common/oauth2/v2.0/authorize?client_id=" . env('MS_CLIENT_ID') . "&redirect_uri=" . $redirectUri . "&login_hint=testuser1%40tf4cg.onmicrosoft.com&response_type=code&scope=offline_access+OnlineMeetings.ReadWrite+openid+Place.Read.All+profile+User.Read+EAS.AccessAsUser.All+EWS.AccessAsUser.All",
            $response->getTargetUrl()
        );
    }

}
