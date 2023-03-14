<template>
  <span>

    <jet-slide-dialogue :show="encounter.show" @close="closeModal">

     <template #title>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="doh-users-header">
                Encounter: {{ encounter.data.uuid }}
                  <a href="" class="ml-12 flex-row justify-end" @click="closeModal"
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
                                    {{ formatDate(encounter.data.scheduled_at, 'DD/MM/YY') }}
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Scheduled time</div>
                                <div class="basis-2/3">
                                    {{ formatDate(encounter.data.scheduled_at, 'H:mm A') }}
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Requestor</div>
                                <div class="basis-2/3">
                                    {{ encounter.data.main_attendee.name }}, <span>{{
                                        encounter.data.main_attendee.email
                                    }}</span>
                                </div>
                            </div>
                            <div v-if="encounter.data.attendees.length > 1">
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Attendees</div>

                                </div>
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Name</div>
                                    <div class="basis-1/3 font-bold">Email</div>
                                </div>
                                <div v-for="item of encounter.data.attendees">
                                    <div class="flex flex-row mb-3" v-if="!item.is_original">
                                        <div class="basis-1/3 ">{{ item.name }}</div>
                                        <div class="basis-1/3 ">{{ item.email }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Channel</div>
                                <div class="basis-2/3">
                                    {{ encounter.data.channel_types_name }}
                                </div>
                            </div>
                            <div v-if="encounter.data.channel_types_name === 'Face to Face'">
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Location</div>
                                    <div class="basis-2/3">
                                        {{ encounter.data.location_details.name }}
                                        , <span>{{ encounter.data.location_details?.address_1 }}</span>
                                        <br>Phone: {{ encounter.data.location_details?.phone }}
                                    </div>
                                </div>
                                <div class="flex flex-row mb-3" v-if="encounter.data.location_file">
                                    <div class="basis-1/3 font-bold">Travel Instructions</div>
                                    <div class="basis-2/3">
                                        <a :href="route('admin.resources.locations.instructions',[ encounter.data.location_file])"
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
                                        {{ encounter.data.place_name }}, <span>{{ encounter.data.place_email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="encounter.data.channel_types_name === 'Virtual'">
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold">Url</div>
                                    <div class="basis-2/3">

                                    </div>
                                </div>
                            </div>
                            <div v-if="encounter.data.channel_types_name === 'Phone'">
                                <div class="flex flex-row mb-3">
                                    <div class="basis-1/3 font-bold"></div>
                                    <div class="basis-2/3">Please call the Requestor
                                        {{ encounter.data.main_attendee?.name }}<br>
                                        on booked phone number {{ encounter.data.main_attendee?.phone_number }}

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
                                    {{ encounter.data.flow_name }}
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">
                                <div class="basis-1/3 font-bold">Objective</div>
                                <div class="basis-2/3">
                                    {{ encounter?.data.flow_objective }}
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
  </span>
</template>

<script>
import {defineComponent, onMounted, ref, reactive} from "vue";
import {VueCollapsiblePanel, VueCollapsiblePanelGroup,} from "@dafcoe/vue-collapsible-panel";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetSlideDialogue from "@/Components/SlideDialogue.vue";
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import '@dafcoe/vue-collapsible-panel/dist/vue-collapsible-panel.css';

export default defineComponent({

    props: {
        encounter: {
            data: Object,
            show: Boolean,
        }
    },

    components: {
        JetButton,
        JetSlideDialogue,
        JetSecondaryButton,
        VueCollapsiblePanel,
        VueCollapsiblePanelGroup,
    },

    setup(props) {
        const closeModal = () => {
            usePage().props.single = {};
            props.encounter.show   = false;
        }

        return {
            closeModal,
        }
    },
});
</script>
