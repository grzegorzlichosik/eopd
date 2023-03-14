<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OAuth\OAuthNylasService;
use App\Models\Place;
use App\Models\PlaceType;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Threadable\StateMachine\Components\StateMachineRunner;

/**
 * @codeCoverageIgnore
 */
class GetResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whencounter:get_resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Get rooms data automatically from the nylas";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __construct(protected OAuthNylasService $nylasService)
    {
        parent::__construct();
    }

    public function handle(): int
    {

        Organisation::whereNotNull("nylas_access_token")
            ->whereNull('is_platform')
            ->with(
                [
                    'places' => function ($query) {
                        return $query->where('place_types_id', PlaceType::RESOURCED);
                    }
                ]
            )
            ->get()
            ->each(function ($organisation) {
                $response = collect($this->getResponse($organisation->nylas_access_token))
                    ->keyBy('email')
                    ->toArray();

                $organisation->places->each(function ($place) use (&$response) {

                    /**
                     * Update existing place from response
                     */
                    if (in_array($place->email, array_keys($response))) {
                        $resource = $response[$place->email];
                        $this->updatePlace($place, $resource);

                        unset($response[$place->email]);

                    } else {
                        /**
                         * No place in response
                         * so set it to inactive
                         */
                        if (in_array($place->tsm_current_state, [Place::STATE_ACTIVE, Place::STATE_INACTIVE])) {
                            $place->transit('place_nylas_update_error');
                        }

                    }
                });

                /**
                 * Create new rooms
                 */
                foreach ($response as $resource) {
                    $place = Place::create(
                        [
                            'email'            => $resource->email,
                            'organisations_id' => $organisation->id,
                            'name'             => $resource->name,
                            'place_types_id'   => PlaceType::RESOURCED,
                            'external_id'      => '',
                            'metadata'         => [
                                'capacity'     => $resource->capacity,
                                'building'     => $resource->building,
                                'floor_name'   => $resource->floor_name,
                                'floor_number' => $resource->floor_number,
                                'name'         => $resource->name,
                            ]
                        ]
                    );

                    $place->transit('place_create');
                }
            });

        return self::SUCCESS;
    }

    private function getResponse(string $token): object
    {
        return $this->nylasService->getAuthResponse('/resources', 'get', [], $token);
    }

    private function updatePlace(Place $place, object $resource): void
    {
        $place->update(
            [
                'email'          => $resource->email,
                'place_types_id' => PlaceType::RESOURCED,
                'external_id'    => '',
                'metadata'       => [
                    'capacity'     => $resource->capacity,
                    'building'     => $resource->building,
                    'floor_name'   => $resource->floor_name,
                    'floor_number' => $resource->floor_number,
                    'name'         => $resource->name,
                ]
            ]
        );

        /**
         * If place exists in payload and was in state Error
         * transit it to the previous state (Active/Inactive)
         */
        if ($place->tsm_current_state === Place::STATE_ERROR) {

            $tsmLog = DB::table('tsm_logs')
                ->where('stateable_id', $place->id)
                ->where('stateable_class', get_class($place))
                ->where('new_state', Place::STATE_ERROR)
                ->latest()
                ->first();

            $transition = $tsmLog->old_state === Place::STATE_ACTIVE
                ? 'place_activate'
                : 'place_deactivate';

            $place->transit($transition);
        }
    }

}
