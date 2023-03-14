<?php

namespace Database\Seeders;

use App\Models\ChannelType;
use App\Models\Flow;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\Pool;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSetupSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();
            /**
             * Get default organisation
             */
            $organisation = Organisation::whereNull('is_platform')->first();

            /**
             * Get Agents and Admins
             */
            $agents = $this->getAgents($organisation);
            $this->getAdmins($organisation);

            $agentsIds = $agents->map(fn($item) => $item->id)->toArray();

            /**
             * Get Pools
             */
            $pools = $this->getPools($organisation);

            $pools->each(function ($pool) use ($agentsIds) {
                $pool->users()->sync($agentsIds);
            });

            /**
             * Get Locations
             */
            $locations = $this->getLocations($organisation);

            /**
             * Get Places
             */
            $places = $this->getPlaces($organisation, $locations);

            /**
             * Create Flows and assign channels
             */
            $flows = $this->getFlows($organisation);

            $flows->each(function ($flow) use ($organisation, $places, $agents) {
                if ($flow->channels->isEmpty()) {
                    $channels = [];
                    foreach ([ChannelType::F2F, ChannelType::WEB, ChannelType::PHONE] as $type) {
                        $channels[] = [
                            'channel_types_id' => $type,
                            'organisations_id' => $organisation->id,
                        ];
                    }

                    $flow->channels()
                        ->createMany($channels)
                        ->each(function ($channel) use ($places) {
                            if ($channel->channel_types_id === ChannelType::F2F) {
                                $channel->places()->sync(
                                    $places->map(fn($item) => $item->id)->toArray()
                                );
                            }
                        });

                    $users = $agents->random(2)
                        ->mapWithKeys(function ($user) {
                            return [
                                $user->id =>
                                    [
                                        'pools_id' => $user->pools->random()->id,
                                    ]
                            ];
                        })
                        ->toArray();

                    $flow->users()->sync($users);
                }
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

    }

    private function getAgents(Organisation $organisation): ?Collection
    {
        $agents = User::where('organisations_id', $organisation->id)->agent()->get();
        if ($agents->isEmpty()) {
            $agents = User::factory(5)->agent()->create(['organisations_id' => $organisation->id]);
        } else {
            $agents->each(function($agent){
                $agent->email_verified_at = now();
                $agent->save();
            });
        }
        return $agents;
    }

    private function getAdmins(Organisation $organisation): ?Collection
    {
        $admins = User::where('organisations_id', $organisation->id)->administrator()->get();
        if ($admins->isEmpty()) {
            $admins = User::factory(2)->admin()->create(['organisations_id' => $organisation->id]);
        }

        return $admins;
    }

    private function getPools(Organisation $organisation): ?Collection
    {
        $pools = Pool::where('organisations_id', $organisation->id)->get();
        if ($pools->isEmpty()) {
            $pools = collect([]);
            for ($i = 1; $i <= 3; $i++) {
                $pools->push(
                    Pool::factory()->create(
                        [
                            'organisations_id' => $organisation->id,
                            'name'             => 'Pool ' . $i,
                        ]
                    )
                );
            }
        }
        return $pools;
    }

    private function getLocations(Organisation $organisation): ?Collection
    {
        $locations = Location::where('organisations_id', $organisation->id)->get();
        if ($locations->isEmpty()) {
            $locations = collect([]);
            for ($i = 1; $i <= 3; $i++) {
                $locations->push(
                    Location::factory()->create(
                        [
                            'organisations_id' => $organisation->id,
                            'name'             => 'Location ' . $i,
                            'short_name'       => 'Loc ' . $i,
                            'files_id'         => null,
                        ])
                );
            }
        }
        return $locations;
    }

    private function getPlaces(Organisation $organisation, Collection $locations): ?Collection
    {
        $places = Place::where('organisations_id', $organisation->id)->get();
        if ($places->isEmpty()) {
            $places = collect([]);
            for ($i = 1; $i <= 5; $i++) {
                $places->push(
                    Place::factory()->create(
                        [
                            'organisations_id' => $organisation->id,
                            'name'             => 'Place ' . $i,
                            'locations_id'     => $locations->random()->id,
                        ]
                    )
                );
            }
        } else {
            $places->each(function($place){
                $place->tsm_current_state = Place::STATE_ACTIVE;
                $place->save();
            });
        }

        return $places;
    }

    private function getFlows(Organisation $organisation): ?Collection
    {
        $flows = Flow::where('organisations_id', $organisation->id)->get();
        if ($flows->isEmpty()) {
            for ($i = 1; $i <= 2; $i++) {
                $flows->push(
                    Flow::factory()->create(
                        [
                            'organisations_id' => $organisation->id,
                            'name'             => 'Flow ' . $i,
                            'objective'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ]
                    )
                );
            }
        }
        return $flows;
    }
}
