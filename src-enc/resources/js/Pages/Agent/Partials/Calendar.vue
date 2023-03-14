<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div style="text-align: left">
            <div class="relative w-min">
                <InputText v-model="searchText" class="focus:shadow-none shadow-none"
                           style="padding-right: 37px;box-shadow: 0 0 0 1px #0000;border-color: #d4d4d8; border-radius: 0"
                           placeholder="Global Search" size="20" @keyup.enter="onSearch"
                           dusk="global-search"/>
                <button class="bg-white p-3 text-gray-400 hover:text-gray-500 absolute"
                        @click="showSearchModal" style="right:0;top:1px;">
                    <i class="fa fa-sliders" aria-hidden="true"></i>
                </button>
                <button class="bg-brand-900 p-3 text-white border border-primary absolute" @click="onSearch"
                        dusk="search">
                    <i class="pi pi-search"></i>
                </button>
            </div>
        </div>
        <div v-if="showSearch" class="flex flex-col bg-white shadow-md p-4"
             style="width: 550px; position: absolute; z-index: 100">
            <div class="p-3">
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <jet-label class="mt-2 text-gray-600">Flows</jet-label>
                    <jet-drop-down-widget class="w-full" v-model="searchFlows" :items="flowsList"
                                          v-bind:key="searchFilter['searchFlows']" name="flows"/>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <jet-label class="mt-2 text-gray-600">Places</jet-label>
                    <jet-drop-down-widget class="w-full" v-model="searchPlaces" :items="placesList"
                                          name="places"/>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <jet-label class="mt-2 text-gray-600">Locations</jet-label>
                    <jet-drop-down-widget class="w-full" v-model="searchLocations" :items="locationsList"
                                          name="locations"/>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <jet-label class="mt-2 text-gray-600">Channels</jet-label>
                    <jet-drop-down-widget class="w-full" v-model="searchChannels" :items="channelList"
                                          name="channels"/>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <jet-label class="mt-2 text-gray-600">Status</jet-label>
                    <jet-drop-down-widget class="w-full" v-model="searchStatus" :items="statusList"
                                          name="status"/>
                </div>
            </div>
            <div>
                <jet-button style="float: right" class="ml-3 mr-4" @click="onSearch">Search
                </jet-button>
                <jet-secondary-button style="float: right" @click="closeSearchModal">Cancel
                </jet-secondary-button>
            </div>
        </div>

        <div style="text-align: right">
            <div style="text-align: end">
                Calendar linked with email:
                <span class="font-semibold">
                    {{ $page.props.user.office_365_email_id }}
                </span>
            </div>
        </div>
    </div>
    <div class="searchToolTip">
        <div v-if="searchFilter['searchValue']"
             class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-blue-300 mb-1 w-fit pill">
            <div class="searchTool">
                <div>
                    <h5>{{ searchFilter['searchValue'] }}</h5>
                </div>
                <div style="margin-left:10px"> <button @click="cancelSearch" class="searchClose">
                    <i class="fa fa-close"></i>
                </button></div>
            </div>
        </div>
        <div v-if="searchFilter['searchFlows']"
             class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-gray-300 mb-1 w-fit pill">
            <div class="searchTool">
                <div>
                    <h5> Flow: {{ searchFilter['searchFlows']['name'] }}</h5>
                </div>
                <div style="margin-left:10px"> <button @click="cancelFlow" class="searchClose">
                    <i class="fa fa-close"></i>
                </button>
                </div>
            </div>
        </div>

        <div v-if="searchFilter['searchPlaces']"
             class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-green-300 mb-1 w-fit pill">
            <div class="searchTool">
                <div>
                    <h5>Place: {{ searchFilter['searchPlaces']['name'] }}</h5>
                </div>
                <div style="margin-left:10px"> <button @click="cancelPlace" class="searchClose">
                    <i class="fa fa-close"></i>
                </button></div>
            </div>
        </div>
        <div v-if="searchFilter['searchLocations']"
             class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-brand-300 mb-1 w-fit pill">
            <div class="searchTool">
                <div>
                    <h5>Location: {{ searchFilter['searchLocations']['name'] }}</h5>
                </div>
                <div style="margin-left:10px"> <button @click="cancelLocation" class="searchClose">
                    <i class="fa fa-close"></i>
                </button></div>
            </div>
        </div>
        <div v-if="searchFilter['searchChannels']"
             class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-yellow-300 mb-1 w-fit pill">
            <div class="searchTool">
                <div>
                    <h5>Channel: {{ searchFilter['searchChannels']['name'] }}</h5>
                </div>
                <div style="margin-left:10px"> <button @click="cancelChannel" class="searchClose">
                    <i class="fa fa-close"></i>
                </button></div>
            </div>
        </div>
        <div v-if="searchFilter['searchStatus']"
             class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-red-300 mb-1 w-fit pill">
            <div class="searchTool">
                <div>
                    <h5>Status: {{ searchFilter['searchStatus']['name'] }}</h5>
                </div>
                <div style="margin-left:10px"> <button @click="cancelStatus" class="searchClose">
                    <i class="fa fa-close"></i>
                </button></div>
            </div>
        </div>
    </div>
    <div>
        {{encounters.length}} Encounters were found matching your search criteria.
    </div>
    <br>
    <div style="position: absolute;
    display: flex;
    flex: 1;
    justify-content: center;
    align-items: center;
    width: 980px !important;
    height: 80vh;
    z-index: 2;" v-if="Loading">
        <ProgressSpinner/>
    </div>

    <calendar-slider :encounter="eventData">
    </calendar-slider>
    <div>
        <FullCalendar :events="events" :options="options" />
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import '@fullcalendar/core/vdom'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid';
import ProgressSpinner from 'primevue/progressspinner';
import CalendarSlider from './CalendarSlider.vue';
import InputText from "primevue/inputtext"; //optional for row
import JetDialogModal from "@/Components/DialogModal.vue";
import JetButton from "@/Components/Button.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetDropDownWidget from "@/Components/DropDownWidget";
import JetInput from "@/Components/Input.vue";
import Paginator from "@/Components/Paginator.vue";
import JetLabel from "@/Components/Label.vue";


export default defineComponent({

    props: {
        event: Object,
        searchValue: String,
        encounters: Object,
        flowsList: Array,
        placesList: Array,
        channelList: Array,
        locationsList: Array,
        statusList: Array,
        searchFilter: Array,
    },

    components: {
        FullCalendar,
        ProgressSpinner,
        CalendarSlider,
        InputText,
        JetDialogModal,
        JetButton,
        JetSecondaryButton,
        JetInput,
        JetButtonSmall,
        Paginator,
        JetLabel,
        JetDropDownWidget
    },

    data() {
        return {
            encounters: 0,
            showSearch: false,
            searchText: this.searchFilter['searchValue'],
            searchFlows: this.searchFilter['searchFlows']?.['uuid'],
            searchPlaces: this.searchFilter['searchPlaces']?.['uuid'],
            searchLocations: this.searchFilter['searchLocations']?.['uuid'],
            searchChannels: this.searchFilter['searchChannels']?.['uuid'],
            searchStatus: this.searchFilter['searchStatus']?.['uuid'],
            Loading: false,
            eventData: [],
            options: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                timeZone: 'UTC',
                initialView: 'timeGridWeek',
                slotMinTime: '07:00:00',
                slotMaxTime: '19:00:00',
                allDaySlot: true,
                nowIndicator: true,
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                loading: this.handleLoading,
                showEventModal: false,
                events: this.encounters,
                eventClick: this.handleClick,
            }
        }
    },

    methods: {
        onSearch() {
            this.showAlert = false;
            this.showSearch = false;
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                place: this.searchPlaces,
                channel: this.searchChannels,
                currentState: this.searchStatus,
                location: this.searchLocations
            };
            this.showResult(payload)

        },

        onSort(event) {
            this.sortField = event.sortField
            this.sortOrder = event.sortOrder
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                place: this.searchPlaces,
                channel: this.searchChannels,
                currentState: this.searchStatus,
                location: this.searchLocations
            };
            this.showResult(payload)

        },

        cancelSearch() {
            this.searchText = ''
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                place: this.searchPlaces,
                channel: this.searchChannels,
                currentState: this.searchStatus,
                location: this.searchLocations
            };
            this.showResult(payload)
        },

        cancelFlow() {
            this.searchFlows = ''
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                place: this.searchPlaces,
                channel: this.searchChannels,
                currentState: this.searchStatus,
                location: this.searchLocations
            };
            this.showResult(payload)
        },


        cancelPlace() {
            this.searchPlaces = ''
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                place: this.searchPlaces,
                channel: this.searchChannels,
                currentState: this.searchStatus,
                location: this.searchLocations
            };
            this.showResult(payload)
        },

        cancelLocation() {
            this.searchLocations = ''
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                place: this.searchPlaces,
                channel: this.searchChannels,
                currentState: this.searchStatus,
                location: this.searchLocations
            };
            this.showResult(payload)
        },

        cancelChannel() {
            this.searchChannels = ''
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                place: this.searchPlaces,
                channel: this.searchChannels,
                currentState: this.searchStatus,
                location: this.searchLocations
            };
            this.showResult(payload)
        },

        cancelStatus() {
            this.searchStatus = ''
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                place: this.searchPlaces,
                channel: this.searchChannels,
                currentState: this.searchStatus,
                location: this.searchLocations
            };
            this.showResult(payload)
        },

        showResult(payload) {
            this.$inertia.get(route('agent.encounters.calendar'), payload, {
                preserveState: false,
            });
        },

        showSearchModal() {
            this.showSearch = true;
        },

        closeSearchModal() {
            this.showSearch = false;
        },

        handleLoading(isLoading) {
            this.Loading = isLoading
        },
        handleClick(info) {
            let Events = {
                data: info.event
            }
            axios.get(route('agent.encounters.show', {
                uuid: Events.data.extendedProps.event_id,

            })).then(response => {
                this.eventData = {
                    show: !this.showEventModal,
                    data: response.data
                }
            });
        }
    },
})
</script>

<style scoped>

@media screen and (max-width: 960px) {
    ::v-deep(.fc-header-toolbar) {
        display: flex;
        flex-wrap: wrap;
    }

}

@media screen and (min-width: 320px) and (max-width: 780px) {
    ::v-deep(.fc-header-toolbar) {
        display: flex;
        flex-wrap: wrap;
    }

    .p-progress-spinner {
        width: 50% !important;
        height: 50%;
    }
}
</style>
