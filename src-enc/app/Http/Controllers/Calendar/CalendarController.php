<?php

namespace App\Http\Controllers\Calendar;

use App\Exceptions\OAuthConnectionException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CalendarService;
use App\Http\Traits\JsonResponse;

class CalendarController extends Controller
{
    use JsonResponse;

    public function __construct(
        protected CalendarService $service
    )
    {
    }
    /**
     *
     * @codeCoverageIgnore
     */
    public function index(Request $request)
    {
        return $this->respond(function () use ($request) {
            return $this->service->getEventsCollection($request->input(), auth()->user());
        });
    }
}
