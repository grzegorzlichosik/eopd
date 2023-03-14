<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Services\NylasService;
use Illuminate\Support\Facades\Redirect;
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
        $redirectUri = env('APP_URL') . '/resources/oauth/callback';
        $url = $this->service->authNative($redirectUri);
        return $url ? Inertia::location($url) : Redirect::back();
    }

    public function retry(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $auth = Organisation::find(auth()->user()->organisations_id);

        $auth->nylas_access_token = null;
        $auth->nylas_account_id = null;
        $auth->nylas_provider = null;
        $auth->microsoft_refresh_token = null;
        $auth->save();

        return Redirect::route('resources.init');

    }
}
