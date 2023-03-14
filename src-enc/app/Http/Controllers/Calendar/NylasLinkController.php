<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Services\NylasService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class NylasLinkController extends Controller
{
    public function __construct(
        protected NylasService $service
    )
    {
    }

    public function authNative()
    {
        $redirectUri = env('APP_URL') . '/calendar/oauth/callback';
        $url = $this->service->authNative($redirectUri);
        return $url ? Inertia::location($url) : Redirect::back();
    }

    public function retry(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $authUser = auth()->user();

        $authUser->nylas_access_token = null;
        $authUser->nylas_account_id = null;
        $authUser->nylas_provider = null;
        $authUser->nylas_primary_calendar_id = null;
        $authUser->microsoft_refresh_token = null;
        $authUser->save();

        return Redirect::route('calendar.init', ['link' => Session::get('link')]);

    }
}
