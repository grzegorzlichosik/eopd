<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        $authUser = auth()->user();

        $share = [
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'toaster' => fn() => $request->session()->get('toaster')
            ],
        ];

        if ($authUser) {
            $share = array_merge($share, [
                'user_last_login'     => $authUser?->latestLogin()?->first()?->created_at,
                'default_date_format' => $authUser?->organisation?->default_date_format ?? 'DD/MM/YYYY',
                'default_time_format' => $authUser?->organisation?->default_time_format ?? 'hh:mm A',
                'default_date_time_format' => $authUser?->organisation?->default_date_format ?? 'DD/MM/YYYY hh:mm A',
            ]);
        }

        return array_merge(parent::share($request), $share);
    }
}
