<template>
  <span>
    <DataTable
        scrollHeight="80%"
        :value="tableData"
        class="mt-3"
        rowEditor="true"
        responsive="true"
        columnResizeMode="fit"
        responsiveLayout="scroll"
        scrollDirection="horizontal"
        @sort="onSort($event)"
    >
      <template #header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
         <div style="text-align: left">
                <div class="relative w-min">
                    <InputText v-model="searchText" class="focus:shadow-none shadow-none"
                               style="padding-right: 37px;box-shadow: 0 0 0 1px #0000;border-color: #d4d4d8; border-radius: 0"
                               placeholder="Global Search" size="20"
                               @keyup.enter="onSearch"
                               dusk="global-search"
                    />
                     <button class="bg-white p-3 text-gray-400 hover:text-gray-500 absolute" @click="showSearchModal"
                             style="right:0;top:1px;">
                            <i class="fa fa-sliders" aria-hidden="true"></i>
                     </button>
                    <button class="bg-brand-900 p-3 text-white border border-primary absolute" @click="onSearch"
                            dusk="search">
                        <i class="pi pi-search"></i>
                    </button>
                </div>
            </div>
            <div
                v-if="showSearch"
                class="flex flex-col bg-white shadow-md p-4"
                style="width: 550px; position: absolute; z-index: 100"
            >
                    <div class="p-3">
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <jet-label class="mt-2 text-gray-600">Flows</jet-label>
                            <jet-drop-down-widget class="w-full" v-model="searchFlows" :items="flowsList"
                                                  v-bind:key="searchFilter['searchFlows']"
                                                  name="flows"/>
                        </div>
                         <div class="grid grid-cols-2 gap-2 mb-2">
                            <jet-label class="mt-2 text-gray-600">Agents</jet-label>
                            <jet-drop-down-widget class="w-full" v-model="searchAgents" :items="agentsList"
                                                  name="agents"/>
                        </div>
                         <div class="grid grid-cols-2 gap-2 mb-2">
                            <jet-label class="mt-2 text-gray-600">Places</jet-label>
                            <jet-drop-down-widget class="w-full" v-model="searchPlaces" :items="placesList"
                                                  name="agents"/>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <jet-label class="mt-2 text-gray-600">Locations</jet-label>
                            <jet-drop-down-widget class="w-full" v-model="searchLocations" :items="locationsList"
                                                  name="locations"/>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <jet-label class="mt-2 text-gray-600">Channels</jet-label>
                            <jet-drop-down-widget class="w-full" v-model="searchChannels" :items="channelList"
                                                  name="locations"/>
                        </div>
                    </div>
                    <div>
                        <jet-button
                            style="float: right"
                            class="ml-3 mr-4"
                            @click="onSearch"
                        >Search
                        </jet-button
                        >
                        <jet-secondary-button
                            style="float: right"
                            @click="closeSearchModal"
                        >Cancel
                        </jet-secondary-button>
                    </div>
                </div>

        </div>
          <div class="searchToolTip">
                <div v-if="searchFilter['searchValue']"
                     class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-blue-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5>{{ searchFilter['searchValue'] }}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelSearch" class="searchClose">
                            <i class="fa fa-close"></i>
                        </button></div>
                    </div>
                </div>
                <div v-if="searchFilter['searchFlows']"
                     class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-gray-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5> Flow: {{ searchFilter['searchFlows']['name'] }}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelFlow" class="searchClose">
                            <i class="fa fa-close"></i>
                        </button>
                        </div>
                    </div>
                </div>

                <div v-if="searchFilter['searchAgents']"
                     class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-orange-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5>Agent: {{ searchFilter['searchAgents']['name'] }}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelAgent" class="searchClose">
                            <i class="fa fa-close"></i>
                        </button></div>
                    </div>
                </div>
                <div v-if="searchFilter['searchPlaces']"
                     class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-green-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5>Place: {{ searchFilter['searchPlaces']['name'] }}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelPlace" class="searchClose">
                            <i class="fa fa-close"></i>
                        </button></div>
                    </div>
                </div>
              <div v-if="searchFilter['searchLocations']"
                   class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-brand-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5>Location: {{ searchFilter['searchLocations']['name'] }}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelLocation" class="searchClose">
                            <i class="fa fa-close"></i>
                        </button></div>
                    </div>
                </div>
               <div v-if="searchFilter['searchChannels']"
                    class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-yellow-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5>Channel: {{ searchFilter['searchChannels']['name'] }}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelChannel" class="searchClose">
                            <i class="fa fa-close"></i>
                        </button></div>
                    </div>
                </div>
            </div>
      </template>
      <Column
          v-for="item of items"
          :rowEditor="true"
          :field="item.field"
          :header="item.header"
          :key="item.field"
          :sortable="(item.header !== 'Requestor') && (item.header !== 'Channel')"
          :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }"
      >
        <div v-if="loading"><ProgressSpinner/></div>

        <template #body="slotProps">
            <div v-if="item.field==='uuid'">
                {{ getUuid(slotProps.data[item.field]) }}
                 <jet-button class="ml-2 bg-gray-300" @click="copyUuid(slotProps.data[item.field])"
                             title="Click here to copy uuid">
                    <i class="fa fa-copy"></i>
                </jet-button>
                <div v-if="slotProps.data[item.field] === copiedUuid">
                     <div>
                         <span v-if="copied" class="text-brand-600 text-sm">
                            <br>Copied uuid.
                        </span>
                     </div>
                </div>
            </div>
            <div v-if="item.field==='flow_name'">{{ slotProps.data[item.field] }}</div>
            <div v-if="item.field==='channel_types_name'">
                <span v-if="slotProps.data[item.field] === 'Face to Face'"
                      class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-blue-300 mb-1 w-fit">
                    {{ slotProps.data[item.field] }}
                </span>
                <span v-else-if="slotProps.data[item.field] === 'Virtual'"
                      class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-orange-300 mb-1 w-fit">
                    {{ slotProps.data[item.field] }}
                </span>
                <span v-else="slotProps.data[item.field] === 'Phone'"
                      class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-green-300 mb-1 w-fit">
                    {{ slotProps.data[item.field] }}
                </span>
            </div>
            <div v-if="item.field==='agent_name'">{{ slotProps.data[item.field] }}</div>
            <div v-if="item.field==='main_attendee'">{{
                    slotProps.data[item.field] ? slotProps.data[item.field]['name'] : ''
                }}</div>
            <div v-if="item.field === 'attendees_count'"  class="flex flex-row">
                <div v-for="attendees in slotProps.data[item.field]" class="px-1">
                    <i class="fa fa-user" style="color: orange;"></i>
                </div>
            </div>
            <div v-if="item.field==='place_name'">{{
                    slotProps.data[item.field]
                }} <span v-if="slotProps.data['location']">({{ slotProps.data['location'] }})</span></div>
            <div v-if="item.field==='scheduled_at'">
                {{ toLocalDate(slotProps.data[item.field], $page.props.default_date_format) }}
                 {{ toLocalTime(slotProps.data[item.field], 'HH:mm') }}
            </div>

        </template>

      </Column>
        <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
                :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
            <template #body="slotProps">
                <div style="display: flex;flex-direction: row;">
                <encounter-attendees-form :encounter="slotProps.data">
                    <jet-button-small
                        dusk="view-attendees-button"
                        id="attendees_list"
                        class="ml-2 justify-end bg-brand-300 hover:bg-brand-900 edit-user"
                    >Attendees
                    </jet-button-small>
                </encounter-attendees-form>

                    <jet-button-small
                        @click="view( slotProps.data.uuid )"
                        dusk="view-button"
                        id="view-button"
                        class="ml-2 justify-end bg-brand-800 hover:bg-brand-900 edit-user"
                    >View
                    </jet-button-small>
                </div>
            </template>
        </Column>
        <template #empty>
            <div class="text-center">No records found.</div>
        </template>
    </DataTable>

    <paginator v-bind:paginationList="paginationList"/>
  </span>
</template>

<script>
import {defineComponent} from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import ColumnGroup from "primevue/columngroup"; //optional for column grouping
import Row from "primevue/row";
import InputText from "primevue/inputtext"; //optional for row
import JetDialogModal from "@/Components/DialogModal.vue";
import ProgressSpinner from 'primevue/progressspinner';
import JetButton from "@/Components/Button.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import SearchFormGenerator from "@/Components/SearchFormGenerator.vue";
import JetDropDownWidget from "@/Components/DropDownWidget";
import JetInput from "@/Components/Input.vue";
import JetCheckbox from "@/Components/Checkbox.vue";
import Paginator from "@/Components/Paginator.vue";
import JetValidationErrors from "@/Components/ValidationErrors.vue";
import JetLabel from "@/Components/Label.vue";
import ConfirmsDialog from "@/Components/ConfirmsDialog.vue";
import EncounterAttendeesForm from "../../../Components/EncounterAttendeesForm.vue";
import {Link} from '@inertiajs/inertia-vue3';

export default defineComponent({

    props: {
        search: String,
        items: Array,
        paginationList: Array,
        advancedSearch: Array,
        tableData: Array,
        rowClass: Function,
        cellIcon: Function,
        listForSelect: Array,
        userRoles: Array,
        errors: Object,
        message: String,
        sortOrder: String,
        sortField: String,
        flowsList: Array,
        agentsList: Array,
        placesList: Array,
        locationsList: Array,
        channelList: Array,
        searchFilter: Array,
    },

    components: {
        JetCheckbox,
        JetDropDownWidget,
        DataTable,
        Column,
        ColumnGroup,
        Row,
        InputText,
        JetDialogModal,
        JetButton,
        JetSecondaryButton,
        SearchFormGenerator,
        JetInput,
        JetButtonSmall,
        Paginator,
        JetValidationErrors,
        JetLabel,
        ConfirmsDialog,
        ProgressSpinner,
        EncounterAttendeesForm,
        Link,

    },
    data() {
        return {
            sortField: '',
            sortOrder: '',
            totalItemsCount: 0,
            value: true,
            reset: false,
            editingRows: [],
            selected: "",
            deactivated: false,
            loading: false,
            showSearch: false,
            searchText: this.searchFilter['searchValue'],
            searchFlows: this.searchFilter['searchFlows']?.['uuid'],
            searchAgents: this.searchFilter['searchAgents']?.['uuid'],
            searchPlaces: this.searchFilter['searchPlaces']?.['uuid'],
            searchLocations: this.searchFilter['searchLocations']?.['uuid'],
            searchChannels: this.searchFilter['searchChannels']?.['uuid'],
            copied: false,
            copiedUuid: ''
        };
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
                agent: this.searchAgents,
                place: this.searchPlaces,
                channel: this.searchChannels,
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
                agent: this.searchAgents,
                place: this.searchPlaces,
                channel: this.searchChannels,
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
                agent: this.searchAgents,
                place: this.searchPlaces,
                channel: this.searchChannels,
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
                agent: this.searchAgents,
                place: this.searchPlaces,
                channel: this.searchChannels,
                location: this.searchLocations
            };
            this.showResult(payload)
        },

        cancelAgent() {
            this.searchAgents = ''
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                flows: this.searchFlows,
                agent: this.searchAgents,
                place: this.searchPlaces,
                channel: this.searchChannels,
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
                agent: this.searchAgents,
                place: this.searchPlaces,
                channel: this.searchChannels,
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
                agent: this.searchAgents,
                place: this.searchPlaces,
                channel: this.searchChannels,
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
                agent: this.searchAgents,
                place: this.searchPlaces,
                channel: this.searchChannels,
                location: this.searchLocations
            };
            this.showResult(payload)
        },

        showResult(payload) {
            this.$inertia.get(route('admin.encounters.cancelled'), payload, {
                preserveState: true,
            });
        },

        showSearchModal() {
            this.showSearch = true;
        },

        closeSearchModal() {
            this.showSearch = false;
        },

        async copyUuid(uuid) {
            try {
                await navigator.clipboard.writeText(uuid);
                this.copied = true;
                this.copiedUuid = uuid;
            } catch ($e) {
                alert('Cannot copy');
            }
        },

        getUuid(uuid) {
            return uuid.length > 5 ? uuid.substring(0, 5) + '...' : uuid;
        },

        view(uuid) {
            this.$inertia.get(route('admin.encounters.show', {uuid: uuid}));
        },



    },

});
</script>

<style>
.p-tabview-title {
    color: #1F2937 !important;
}

.align-right {
    text-align: right !important;
}

.align-center {
    text-align: center !important;
}

.p-datatable th[class*="align-"] .p-column-header-content {
    display: inline-flex;
}

.searchToolTip {
    display: flex !important;
    flex-direction: row !important;
    width: 100% !important;
    justify-content: flex-start !important;
    align-items: flex-start !important;
    margin-top: 10px !important;
}

.searchTool {
    display: flex !important;
    flex-direction: row !important;
    justify-content: space-around !important;
    align-items: center !important;
    width: 100% !important;
    padding: 5px;
}

.pill {
    border-radius: 16px !important;
    height: auto !important;
    align-items: center !important;
    padding-left: 10px !important;
    width: auto !important;
    justify-content: space-around;
    padding-right: 5px;
    margin-left: 10px;
}

.searchClose {
    background-color: white;
    border-radius: 100% !important;
    cursor: pointer;
    width: 17px !important;
    height: 18px !important;
    align-items: center !important;
    justify-content: center !important;
    display: flex;
}

</style>
