<?php

namespace Tests\Unit\Models;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\EncounterAttendee;
use App\Models\FlowType;
use App\Models\Flow;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\Pool;
use App\Models\User;
use Tests\TestCase;
use Threadable\StateMachine\Exceptions\IllegalStateTransitionException;

class EncounterTest extends TestCase
{
    public function test_encounter(): void
    {
        Encounter::factory(10)->create()->each(function ($encounter) {
            $this->assertDatabaseHas(
                'encounters',
                [
                    'uuid'             => $encounter->uuid,
                    'organisations_id' => $encounter->organisations_id
                ]
            );
        });

        $encounter = Flow::factory()->create();
        $this->assertModelExists($encounter);

        $encounter = Flow::factory()->create();
        $encounter->delete();
        $this->assertModelMissing($encounter);
    }

    public function test_encounter_organisation_relation(): void
    {
        $encounter = Encounter::factory()->create();
        $this->assertModelExists($encounter);
        $this->assertInstanceOf(Organisation::class, $encounter->organisation);
        $this->assertEquals($encounter->organisations_id, $encounter->organisation->id);
    }

    public function test_encounter_agent_relation(): void
    {
        $encounter = Encounter::factory()->create();
        $this->assertModelExists($encounter);
        $this->assertInstanceOf(User::class, $encounter->agent);
        $this->assertEquals($encounter->agent_id, $encounter->agent->id);
    }

    public function test_encounter_flow_relation(): void
    {
        $encounter = Encounter::factory()->create();
        $this->assertModelExists($encounter);
        $this->assertInstanceOf(Flow::class, $encounter->flow);
        $this->assertEquals($encounter->flows_id, $encounter->flow->id);
    }

    public function test_encounter_channel_relation(): void
    {
        $encounter = Encounter::factory()->create();
        $this->assertModelExists($encounter);
        $this->assertInstanceOf(Channel::class, $encounter->channel);
        $this->assertEquals($encounter->channels_id, $encounter->channel->id);
    }

    public function test_encounter_channel_type_relation(): void
    {
        $encounter = Encounter::factory()->create();
        $this->assertModelExists($encounter);
        $this->assertInstanceOf(ChannelType::class, $encounter->channel_type);
        $this->assertEquals($encounter->channel_types_id, $encounter->channel_type->id);
    }

    public function test_encounter_places_relation(): void
    {
        $encounter = Encounter::factory()->create();
        $this->assertModelExists($encounter);
        $this->assertInstanceOf(Place::class, $encounter->place);
        $this->assertEquals($encounter->places_id, $encounter->place->id);
    }

    public function test_encounter_attendees_relation(): void
    {
        $encounter = Encounter::factory()->create();

        EncounterAttendee::factory(10)->create(
            [
                'encounters_id' => $encounter->id,
            ]
        )
            ->each(function ($attendee) use ($encounter) {
                $this->assertDatabaseHas(
                    'encounter_attendees',
                    [
                        'encounters_id' => $encounter->id,
                    ]
                );
            });

        $this->assertEquals(10, $encounter->attendees->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $encounter->attendees);
        $this->assertInstanceOf(EncounterAttendee::class, $encounter->attendees->first());
    }

    protected function getEncounter($status, $role = 'admin'): Encounter
    {
        $user = User::factory()->$role()->create();

        $this->actingAs($user);

        $encounter = [
            'tsm_current_state' => $status,
            'organisations_id'  => $user->organisations_id,
        ];

        if($role === 'agent') {
            $encounter['agent_id'] = $user->id;
        }

        return Encounter::factory()->create($encounter);
    }

    public function test_get_reference_id()
    {
        $encounter = $this->getEncounter(Encounter::STATE_SCHEDULED);

        $this->assertNull($encounter->getReferenceId());
    }

    public function test_state_initial()
    {
        $encounter = $this->getEncounter(Encounter::STATE_INITIAL);
        $transitions = array_keys($encounter->getAvailableTransitions());
        $this->assertEqualsCanonicalizing(
            [
                'encounter_create',
            ],
            $transitions
        );
    }

    public function test_state_scheduled()
    {
        $encounter = $this->getEncounter(Encounter::STATE_SCHEDULED);
        $transitions = array_keys($encounter->getAvailableTransitions());
        $this->assertEqualsCanonicalizing(
            [
                'encounter_accept',
                'encounter_re_schedule',
                'encounter_auto_re_schedule',
                'encounter_finish',
                'encounter_administrator_cancel',
                'encounter_original_attendee_cancel',
            ],
            $transitions
        );
    }

    public function test_state_pending_auto_re_scheduling()
    {
        $encounter = $this->getEncounter(Encounter::STATE_PENDING_AUTO_RE_SCHEDULING);
        $transitions = array_keys($encounter->getAvailableTransitions());
        $this->assertEqualsCanonicalizing(
            [
                'encounter_re_schedule',
                'encounter_escalate',
            ],
            $transitions
        );
    }

    public function test_state_pending_manual_re_scheduling()
    {
        $encounter = $this->getEncounter(Encounter::STATE_PENDING_MANUAL_RE_SCHEDULING);
        $transitions = array_keys($encounter->getAvailableTransitions());
        $this->assertEqualsCanonicalizing(
            [
                'encounter_re_schedule',
            ],
            $transitions
        );
    }

    public function test_state_finished()
    {
        $encounter = $this->getEncounter(Encounter::STATE_FINISHED);
        $transitions = array_keys($encounter->getAvailableTransitions());
        $this->assertEqualsCanonicalizing([], $transitions);
    }

    public function test_state_cancelled()
    {
        $encounter = $this->getEncounter(Encounter::STATE_CANCELLED);
        $transitions = array_keys($encounter->getAvailableTransitions());
        $this->assertEqualsCanonicalizing([], $transitions);
    }

    public function test_admin_cannot_transit_from_finished_to_scheduled()
    {
        $error = null;
        $encounter = $this->getEncounter(Encounter::STATE_CANCELLED);
        try {
            $encounter->transit('encounter_re_schedule');
        } catch (IllegalStateTransitionException $e) {
            $error = $e;
        }
        $this->assertNotNull($error);
        $this->assertEquals(Encounter::STATE_CANCELLED, $encounter->getTsmState()->name);
    }

    public function test_admin_can_transit_from_scheduled_to_finished()
    {
        $error = null;
        $encounter = $this->getEncounter(Encounter::STATE_SCHEDULED);
        try {
            $encounter->transit('encounter_finish');
        } catch (IllegalStateTransitionException $e) {
            $error = $e;
        }
        $this->assertNull($error);
        $this->assertEquals(Encounter::STATE_FINISHED, $encounter->getTsmState()->name);
    }

    public function test_agent_can_transit_from_scheduled_to_cancelled()
    {
        $error = null;
        $encounter = $this->getEncounter(Encounter::STATE_SCHEDULED, 'agent');

        try {
            $encounter->transit('encounter_agent_cancel');
        } catch (IllegalStateTransitionException $e) {
            $error = $e;
        }
        $this->assertNull($error);
        $this->assertEquals(Encounter::STATE_CANCELLED, $encounter->getTsmState()->name);
    }

    public function test_agent_cannot_transit_from_scheduled_to_cancelled_via_encounter_administrator_cancel_transition()
    {
        $error = null;
        $encounter = $this->getEncounter(Encounter::STATE_SCHEDULED, 'agent');

        try {
            $encounter->transit('encounter_administrator_cancel');
        } catch (IllegalStateTransitionException $e) {
            $error = $e;
        }

        $this->assertNotNull($error);
        $this->assertEquals(Encounter::STATE_SCHEDULED, $encounter->getTsmState()->name);
    }

}
