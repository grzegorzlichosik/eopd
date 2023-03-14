<template>
    <DataTable
        scrollHeight="80%"
        :value="tableData"
        class="mt-3"
        rowEditor="false"
        responsive="true"
        columnResizeMode="fit"
        responsiveLayout="scroll"
        scrollDirection="horizontal"
        @sort="onSort($event)"
    >
        <template #header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div style="text-align: left">
                    <div v-if="advancedSearch" class="relative w-min">
                        <InputText v-model="searchText" class="focus:shadow-none shadow-none"
                                   style="padding-right: 37px;box-shadow: 0 0 0 1px #0000;border-color: #d4d4d8; border-radius: 0"
                                   placeholder="Global Search" size="20"
                                   dusk="global-search"
                                   @keyup.enter="onSearch"
                        />
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
                    <search-form-generator v-bind:advancedSearch="advancedSearch"/>
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
                <div style="text-align: right">
                </div>
            </div>
            <div class="searchToolTip">
                <div v-if="searchValue"  class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-blue-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5>{{searchValue}}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelSearch" class="searchClose">
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
            :sortable="item.header !== 'Creator Status'"
            :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }"
        >

            <template #body="slotProps">
                <div v-if="item.field=='name'">{{ slotProps.data[item.field] }}</div>
                <div v-if="item.field=='master_calendar_email'">
                    <span v-if="slotProps.data.master_calendar_email">
                        <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-green-300 mb-1 w-fit">Connected</div>
                    </span>
                    <span v-else>
                        <div class="text-gray-100 text-xs font-semibold px-1.5 py-1 rounded bg-red-400 mb-1 w-fit">Not Connected</div>
                    </span>
                </div>
                <div v-if="item.field=='master_calendar_nylas_account_id'">
                    <span v-if="slotProps.data.master_calendar_nylas_account_id">
                        <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-green-300 mb-1 w-fit">Connected</div>
                    </span>
                    <span v-else-if="slotProps.data.master_calendar_email">
                        <div class="text-gray-100 text-xs font-semibold px-1.5 py-1 rounded bg-red-400 mb-1 w-fit">Not Connected</div>
                    </span>
                    <span v-else>
                        <div class="text-gray-100 text-xs font-semibold px-1.5 py-1 rounded bg-orange-400 mb-1 w-fit">N/A</div>
                    </span>
                </div>
                <div v-if="item.field=='phone_number'">{{ slotProps.data[item.field] }}</div>
                <div v-if="item.field=='users_count'">
                    <div class="text-white text-xs font-semibold px-1.5 py-1 rounded bg-brand-800 w-fit">
                        {{ slotProps.data[item.field] }}
                    </div>
                </div>
                <div v-if="item.field=='created_by'">
                    {{ slotProps.data['created_by'] ? slotProps.data['created_by']['name'] : '' }}
                </div>
                <div v-if="item.field=='creator_status' && slotProps.data['created_by']">
                    <span v-if="slotProps.data['created_by']['email_verified_at']">
                        <div
                            class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-green-300 w-fit">Active</div>
                    </span>
                    <span v-else>
                        <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-yellow-300 w-fit">Pending Activation</div>
                    </span>
                </div>
                <div v-else></div>
                <div v-if="item.field === 'created_at'">
                    {{
                        slotProps.data[item.field]
                            ?
                            toLocalDate(slotProps.data[item.field], $page.props.default_date_format)
                            : ""
                    }}
                </div>

            </template>
        </Column>
        <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
                :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
            <template #body="slotProps">
                <confirms-dialog v-if="!slotProps.data.master_calendar_email"
                    @confirmed="createMasterCalendar(slotProps.data.uuid)"
                    :title="'Create Google Master Calendar for Organisation: ' + slotProps.data.name"
                    :content="
              'Are you sure you want to create Google Master Calendar for organisation <strong>' +
              slotProps.data.name +
              '?</strong'
            "
                >
                    <jet-button-small dusk="create-master-calendar-button"

                                      class="
                ml-3
                justify-end
                hover:bg-blue-700
                active:bg-blue-900
                bg-blue-600
              "
                    >
                        Connect GSuite
                    </jet-button-small>
                </confirms-dialog>
                <div style="display: flex; flex-direction: row; justify-content: space-around;">

                    <master-calendar-password-dialog :organisation="slotProps.data"
                                                     v-if="slotProps.data.master_calendar_email">
                        <jet-button-small
                            dusk="show-master-calendar-details"
                            class="justify-end
                hover:bg-green-700
                active:bg-green-900
                bg-green-500"
                        >Show GSuite details
                        </jet-button-small>
                    </master-calendar-password-dialog>
                    <a target="_blank" :href="route('platform.organisations.master',  {uuid:slotProps.data.uuid})"
                       v-if="slotProps.data.master_calendar_email && !slotProps.data.master_calendar_nylas_account_id">
                        <jet-button-small
                            dusk="nylas-connect"
                            class="justify-end
                 hover:bg-orange-700
                active:bg-orange-900
                bg-orange-600 ml-2"

                        >Connect to Nylas
                        </jet-button-small>
                    </a>

                    <confirms-dialog v-if="slotProps.data.master_calendar_email"
                                     @confirmed="deleteMasterCalendar(slotProps.data.uuid)"
                                     :title="'Disconnect Master Calendar from Organisation: ' + slotProps.data.name"
                                     :content="
              'Are you sure you want to disconnect master calendar from organisation <strong>' +
              slotProps.data.name +
              '?</strong'
            "
                    >
                        <jet-button-small dusk="delete-master-calendar-button"

                                          class="
                ml-3
                justify-end
                hover:bg-red-700
                active:bg-red-900
                bg-red-600
              "
                        >
                            Disconnect Master Calendar
                        </jet-button-small>
                    </confirms-dialog>


                </div>
            </template>
        </Column>
        <template #empty>
            <div class="text-center">No records found.</div>
        </template>
    </DataTable>
    <paginator v-bind:paginationList="paginationList" class="mb-4"/>
</template>

<script>
import {defineComponent} from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import ColumnGroup from "primevue/columngroup"; //optional for column grouping
import Row from "primevue/row";
import InputText from "primevue/inputtext"; //optional for row
import JetDialogModal from "@/Components/DialogModal.vue";
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
import MasterCalendarPasswordDialog from "@/Components/MasterCalendarPasswordDialog.vue";

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
        errors: Object,
        message: String,
        sortOrder: String,
        sortField: String,
        searchValue: String,
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
        MasterCalendarPasswordDialog,
    },
    methods: {
        onSearch() {
            this.showAlert = false;
            this.showSearch = false;
            let payload = {search: this.searchText, sortField: this.sortField, sortOrder: this.sortOrder};

            this.advancedSearch.forEach((aSearch) => {
                payload[aSearch.name] = aSearch.model;
            });

            this.showResult(payload)
        },

        createMasterCalendar(uuid) {
            this.$inertia.put(route('platform.organisations.master_calendar.create', {uuid: uuid}));
        },

        deleteMasterCalendar(uuid) {
            this.$inertia.delete(route('platform.organisations.master_calendar.delete', {uuid: uuid}));
        },

        onSort(event) {
            this.sortField = event.sortField
            this.sortOrder = event.sortOrder
            this.onSearch()
        },

        showSearchModal() {
            this.showSearch = true;
        },

        closeSearchModal() {
            this.showSearch = false;
        },

        cancelSearch() {
            this.searchText = '';
            let payload = {
                search: '', sortField: this.sortField, sortOrder: this.sortOrder
            };
            this.showResult(payload)
        },

        showResult(payload) {
            this.$inertia.get(route('platform.organisations.index'), payload, {
                preserveState: true,
            });
        },

    },
    data() {
        return {
            sortField: "",
            sortOrder: "",
            totalItemsCount: 0,
            value: true,
            reset: false,
            editingRows: [],
            selected: "",
            deactivated: false,
            showSearch: false,
            searchText: this.search,
        };
    },
});
</script>

<style>
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
    flex-direction: row!important;
    width:100% !important;
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

