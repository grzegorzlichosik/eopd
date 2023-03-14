<template>

    <app-layout title="Administration - Organisation - Schedule Management">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="doh-users-header">
                Encounter: {{ encounter.uuid }}
                <span v-if="encounter.tsm_current_state=== 'Attention Required'"
                      class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-red-300 mb-1 w-fit">
                                    {{ encounter.tsm_current_state }}
                                </span>
                <span v-else-if="encounter.tsm_current_state === 'Awaiting Confirmation'"
                      class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-orange-300 mb-1 w-fit">
                                    {{ encounter.tsm_current_state }}
                                </span>
                <span v-else-if="encounter.tsm_current_state === 'Confirmed'"
                      class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-green-300 mb-1 w-fit">
                                    {{ encounter.tsm_current_state }}
                                 </span>
                <span v-else-if="encounter.tsm_current_state === 'Cancelled'"
                      class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-red-600 mb-1 w-fit">
                                    {{ encounter.tsm_current_state }}
                                 </span>
                <span v-else-if="encounter.tsm_current_state === 'Finished'"
                      class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-blue-600 mb-1 w-fit">
                                    {{ encounter.tsm_current_state }}
                                 </span>
                <span v-else
                      class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-green-600 mb-1 w-fit">
                                    {{ encounter.tsm_current_state }}
                                 </span>
            </h2>
        </template>

        <div class="py-12">
            <vue-collapsible-panel-group>
                <vue-collapsible-panel id="flows-details">
                    <template #title>
                        <header3 class="py-4">Flows details</header3>
                    </template>
                    <template #content>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Name</div>
                            <div class="basis-2/3">
                                {{ encounter.flow.name }}
                            </div>
                        </div>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Objective</div>
                            <div class="basis-2/3">
                                {{ encounter.flow.objective }}
                            </div>
                        </div>
                    </template>
                </vue-collapsible-panel>
                <vue-collapsible-panel :expanded="false" id="encounter-details">
                    <template #title>
                        <header3 class="py-4">Encounter (Event Booking) details</header3>
                    </template>
                    <template #content>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Agent name</div>
                            <div class="basis-2/3">
                                {{ encounter.agent.name }}
                            </div>
                        </div>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Email</div>
                            <div class="basis-2/3">
                                {{ encounter.agent.email }}
                            </div>
                        </div>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Scheduled date</div>
                            <div class="basis-2/3">
                                {{ toLocalDate(encounter.scheduled_at, 'DD/MM/YY') }}
                            </div>
                        </div>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Scheduled time</div>
                            <div class="basis-2/3">
                                {{ toLocalTime(encounter.scheduled_at, 'hh:mm A') }}
                            </div>
                        </div>

                    </template>
                </vue-collapsible-panel>
                <vue-collapsible-panel :expanded="false" id="people-details">
                    <template #title>
                        <header3 class="py-4">Peoples in encounter</header3>
                    </template>
                    <template #content>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Name</div>
                            <div class="basis-1/3 font-bold">Email</div>
                            <div class="basis-1/3 font-bold">Status</div>
                        </div>
                        <div v-for="item of encounter.attendees">
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 ">{{ item.name }} <span v-if="item.is_original"> (Requestor)</span></div>
                                <div class="basis-1/3 ">{{ item.email }}</div>
                                <div class="basis-1/3 ">
                                    <div v-if="item.is_accepted">Accepted</div>
                                    <div v-else>Pending</div>
                                </div>
                            </div>
                        </div>
                    </template>
                </vue-collapsible-panel>
                <vue-collapsible-panel :expanded="false" id="places-details">
                    <template #title>
                        <header3 class="py-4">Places in encounter</header3>
                    </template>
                    <template #content>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Name</div>
                            <div class="basis-2/3">
                                {{ encounter.place.name}}
                            </div>
                        </div>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Email</div>
                            <div class="basis-2/3">
                                {{ encounter.place.email}}
                            </div>
                        </div>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3 font-bold">Location</div>
                            <div class="basis-2/3">
                                {{ location.name }}<br>
                                {{ location.short_name }}<br>
                                {{ location.address_1 }}<br>
                                {{ location.postcode }} <br>
                                {{ location.city_town }}<br>
                                {{ location.phone }}
                            </div>
                        </div>

                    </template>
                </vue-collapsible-panel>
                <vue-collapsible-panel :expanded="false" id="calendar">
                    <template #title>
                        <header3 class="py-4">Calendar</header3>
                    </template>
                    <template #content>
                        <div class="flex flex-row mb-3">
                            <div v-if="error" style="width: 100% !important;">{{ error }}</div>
                            <div v-else style="width: 100% !important;">
                                <FullCalendar :events="events" :options="options"/>
                            </div>
                        </div>
                    </template>
                </vue-collapsible-panel>
            </vue-collapsible-panel-group>
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import {VueCollapsiblePanel, VueCollapsiblePanelGroup,} from "@dafcoe/vue-collapsible-panel";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetLabel from "@/Components/Label.vue";
import '@dafcoe/vue-collapsible-panel/dist/vue-collapsible-panel.css';
import '@fullcalendar/core/vdom'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid';
import moment from 'moment'

export default defineComponent({
    props: {
        encounter: Object,
        location: Object,
        error: String,
    },

    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
        JetLabel,
        VueCollapsiblePanelGroup,
        VueCollapsiblePanel,
        FullCalendar,
    },

    data() {
        let encounteUuid = this.encounter.uuid
        return {
            Loading: false,
            eventData: [],
            options: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialDate : moment(String(this.encounter.scheduled_at)).format('YYYYMMDD'),
                timeZone: 'UTC',
                initialView: 'timeGridDay',
                slotMinTime: '07:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                nowIndicator: true,
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                loading: this.handleLoading,
                showEventModal: false,
                eventSources: [
                    {
                        events(date, callback) {
                            axios.post(route('admin.encounters.calendar', {uuid: encounteUuid}), {
                                start: date.startStr,
                                end: date.endStr
                            }).then(response => {
                                callback(response.data.data)
                            })
                        }
                    }
                ],
                eventClick: this.handleClick,
            }
        }
    },

    methods: {

        handleLoading(isLoading) {
            this.Loading = isLoading
        },
        handleClick(info) {
            this.eventData = {
                show: !this.showEventModal,
                data: info.event
            };
        }
    }
});

</script>


