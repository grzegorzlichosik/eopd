<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConfirmableTwoFactorAuthenticationStatusController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $twoFactorAuthenticationConfirmedAt = $request->session()
            ->get('auth.two_factor_authentication_confirmed_at', 0);

        $twoFactorTimeout =  $request->input('seconds', config('auth.two_factor_timeout', 60));

        return response()->json([
            'confirmed' => (time() - $twoFactorAuthenticationConfirmedAt) < $twoFactorTimeout,
        ]);
    }
}
