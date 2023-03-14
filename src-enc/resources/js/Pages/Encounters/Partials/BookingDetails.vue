<template>
    <div class="p-2 bg-white border-border" style="min-height: 33em">
        <div class="max-w-5xl mx-auto py-2">
            <h1 class="pb-3 text-3xl text-center text-bold">{{ event.flow_name }}</h1>
        </div>
        <h3 class="pb-3 mb-5 text-center">{{ event.flow_objective }}</h3>
        <div class="flex flex-row mb-3">
            <div class="basis-1/3 font-bold">Scheduled on</div>
            <div class="basis-2/3">: {{ toLocalDate(event.scheduled_at, event.organisation.default_date_format) }}
            </div>
        </div>
        <div class="flex flex-row mb-3">
            <div class="basis-1/3 font-bold">Scheduled time</div>
            <div class="basis-2/3">
                <span>: {{ toLocalTime(event.scheduled_at,event.organisation.default_time_format) }}</span>
                <span> to </span>
                <span>{{ toLocalTime(event.ends_at, event.organisation.default_time_format) }}</span>
            </div>
        </div>
        <div class="flex flex-row mb-3">
            <div class="basis-1/3 font-bold">Status</div>
            <div class="basis-2/3">:
                <span v-if="event.tsm_current_state === 'Cancelled' || event.tsm_current_state === 'Finished'"
                      class="text-white text-xs font-semibold px-1.5 py-1 rounded  mb-1 w-fit"
                      :class="{
                    'bg-red-600': event.tsm_current_state === 'Cancelled',
                    'bg-blue-600': event.tsm_current_state === 'Finished'
                      }"
                >
                    {{event.tsm_current_state}}
                </span>
                <span v-else class="text-white text-xs font-semibold px-1.5 py-1 rounded bg-green-600 mb-1 w-fit">{{event.tsm_current_state}}</span>
            </div>
        </div>
        <div class="flex flex-row mb-3">
            <div class="basis-1/3 font-bold">Channel</div>
            <div class="basis-2/3">: {{ event.channel?.type?.name}}

            </div>
        </div>
        <div class="flex flex-row mb-3">
            <div class="basis-1/3 font-bold">Max Participants</div>
            <div class="basis-2/3">: {{ event.channel?.max_participants}}

            </div>
        </div>
        <div class="flex flex-row mb-3" v-if="event.place">
            <div class="basis-1/3 font-bold">Place name</div>
            <div class="basis-2/3">: {{ event.place?.name }}</div>
        </div>
        <div class="flex flex-row mb-3" v-if="event.place?.location">
            <div class="basis-1/3 font-bold">Location</div>
            <div class="basis-2/3">: {{ event.place?.location?.name }}<br>
                <span> {{ event.place?.location?.address_1 }}</span><br>
                <span> {{ event.place?.location?.postcode }}</span><br>
                <span> {{ event.place?.location?.city_town }}</span><br>
                <span> Phone: {{ event.place?.location?.phone }}</span><br>
            </div>
        </div>
        <div class="flex flex-row mb-3">
            <div class="basis-1/3 font-bold">Organisation</div>
            <div class="basis-2/3">: {{ event.organisation.name}}</div>
        </div>

        <hr class="my-5">

        <div class="flex flex-row mb-3">
            <div class="basis-1/3 font-bold">Attendees List</div>
        </div>
        <div class="flex flex-row mb-3">
            <div class="basis-1/3">Name</div>
            <div class="basis-1/3">Email</div>
            <div class="basis-1/3">Accepted</div>
        </div>
        <div class="flex flex-row mb-3" v-for="attendee in event.attendees">
            <div class="basis-1/3">{{ attendee.name }}
                <span v-if="attendee.is_original">(Requestor)</span>
            </div>
            <div class="basis-1/3">{{ attendee.email }}
            </div>
            <div class="basis-1/3">
                <i class="fa fa-check text-green-500" v-if="attendee.is_accepted"></i>
                <i class="fa fa-close text-red-500" v-else></i>
            </div>
        </div>
    </div>

</template>

<script>

import {defineComponent} from 'vue'
import {useForm} from "@inertiajs/inertia-vue3";
import DatePicker from "@/Components/DatePicker.vue";
import JetLabel from "@/Components/Label.vue";
import JetInput from "@/Components/Input.vue";
import JetInputError from "@/Components/InputError.vue";
import JetDropDownWidget from "@/Components/DropDownWidget";
import JetButton from "@/Components/Button.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import Calendar from 'primevue/calendar';
import SelectButton from 'primevue/selectbutton';
import ProgressSpinner from 'primevue/progressspinner';
import moment from 'moment';
import NavLink from "@/Components/NavLink.vue";

export default defineComponent({
    components: {
        DatePicker,
        JetLabel,
        JetInput,
        JetInputError,
        JetDropDownWidget,
        JetButton,
        JetSecondaryButton,
        JetButtonSmall,
        Calendar,
        SelectButton,
        ProgressSpinner,
        useForm,
        NavLink,
    },
    props: {
        event: Object
    },

})
</script>
<style>
.p-inputtext,
.p-togglebutton,
.p-selectbutton,
.p-inputgroup {
    box-shadow: none;
}

.p-button:focus {
    box-shadow: none;
    outline: none;
}

</style>
