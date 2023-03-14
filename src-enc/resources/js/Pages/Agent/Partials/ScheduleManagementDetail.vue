<template>
    <span @click="startUpdating">
        <slot/>
    </span>
    <jet-slide-dialogue :show="updating" modal-id="user" @close="closeModal">
        <template #title>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="doh-users-header">
                Encounter: {{ encounter.uuid }}
                <a href="" class="ml-12 justify-end" @click="closeModal"
                ><i class="fa fa-close"></i></a>
            </h2>
        </template>
        <template #body class="mx-4">
            <div class="py-12">
                <vue-collapsible-panel-group>
                    <vue-collapsible-panel>
                        <template #title>
                            <header3 class="py-4">Encounter</header3>
                        </template>
                        <template #content>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Scheduled date</div>
                                <div class="basis-2/3">
                                    {{ formatDate(encounter.scheduled_at, 'DD/MM/YY') }}
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Scheduled time</div>
                                <div class="basis-2/3">
                                    {{ formatDate(encounter.scheduled_at, 'H:mm A') }}
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Requestor</div>
                                <div class="basis-2/3">
                                    {{ encounter.main_attendee.name }}, <span>{{ encounter.main_attendee.email }}</span>
                                </div>
                            </div>
                            <div v-if="encounter.attendees.length > 1">
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Attendees</div>

                                </div>
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Name</div>
                                    <div class="basis-1/3 font-bold">Email</div>
                                    <div class="basis-1/3 font-bold">Status</div>
                                </div>
                                <div v-for="item of encounter.attendees">
                                    <div class="flex flex-row mb-3" v-if="!item.is_original">
                                        <div class="basis-1/3 ">{{ item.name }}</div>
                                        <div class="basis-1/3 ">{{ item.email }}</div>
                                        <div class="basis-1/3 ">
                                            <div v-if="item.is_accepted">Accepted</div>
                                            <div v-else>Pending</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Channel</div>
                                <div class="basis-2/3">
                                    {{ encounter.channel_types_name }}
                                </div>
                            </div>
                            <div v-if="encounter.channel_types_name === 'Face to Face'">
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Location</div>
                                    <div class="basis-2/3">
                                        {{ encounter.location }}, <span>{{ encounter.location_details?.address_1 }}</span>
                                        <br>Phone: {{ encounter.location_details?.phone }}
                                    </div>
                                </div>
                                <div class="flex flex-row mb-3" v-if="encounter.location_file">
                                    <div class="basis-1/3 font-bold">Travel Instructions</div>
                                    <div class="basis-2/3">
                                        <a :href="route('admin.resources.locations.instructions',[ encounter.location_file])"
                                           class="
                   shadow text-secondary inline-flex items-center px-2 py-1 border border-transparent rounded-md
                   font-semibold text-xs text-white tracking-widest focus:outline-none focus:border-gray-900 focus:ring
                   focus:ring-gray-300 disabled:opacity-25 transition ml-2 justify-end bg-green-500 hover:bg-green-600"
                                        >
                                            <i class="fa fa-download mr-2"></i>Download
                                        </a>
                                    </div>
                                </div>
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Place</div>
                                    <div class="basis-2/3">
                                        {{ encounter.place_name }}, <span>{{ encounter.place_email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="encounter.channel_types_name === 'Virtual'">
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Url</div>
                                    <div class="basis-2/3">

                                    </div>
                                </div>
                            </div>
                            <div v-if="encounter.channel_types_name === 'Phone'">
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold"></div>
                                    <div class="basis-2/3">Please call the Requestor
                                        {{ encounter.main_attendee?.name }}<br>
                                        on booked phone number {{ encounter.main_attendee?.phone_number }}

                                    </div>
                                </div>
                            </div>
                        </template>
                    </vue-collapsible-panel>
                    <vue-collapsible-panel :expanded="false">
                        <template #title>
                            <header3 class="py-4">Context</header3>
                        </template>
                        <template #content>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Name</div>
                                <div class="basis-2/3">
                                    {{ encounter.flow_name }}
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Objective</div>
                                <div class="basis-2/3">
                                    {{ encounter?.flow_objective }}
                                </div>
                            </div>
                        </template>
                    </vue-collapsible-panel>

                </vue-collapsible-panel-group>
            </div>
        </template>
        <template #footer>
        </template>
    </jet-slide-dialogue>
</template>

<script>
import {defineComponent, onMounted, ref, reactive, computed} from "vue";
import {VueCollapsiblePanel, VueCollapsiblePanelGroup,} from "@dafcoe/vue-collapsible-panel";

import JetSlideDialogue from "@/Components/SlideDialogue.vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetLabel from "@/Components/Label.vue";
import JetButton from '@/Components/Button.vue';
import '@dafcoe/vue-collapsible-panel/dist/vue-collapsible-panel.css';
import moment from 'moment'
import {usePage} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";

export default defineComponent({
    props: {
        uuid: String,
        encounter: Object,
        encounterEvents: Object,
        location: Object,
        error: String,
        showEncounter: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["hideModal"],
    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
        JetLabel,
        VueCollapsiblePanelGroup,
        VueCollapsiblePanel,
        JetSlideDialogue,
        JetButton,
    },

    data() {
        return {
            Loading: false,
        }
    },

    setup(props, context) {
        let encounterEvents = reactive([]);
        const updating = ref(false);
        const startUpdating = () => {
            updating.value = true;
        }
        const closeModal = () => {
            usePage().props.value.errors = {};
            updating.value = false;
        }

        return {
            startUpdating,
            closeModal,
            updating,
        }
    },
});

</script>


