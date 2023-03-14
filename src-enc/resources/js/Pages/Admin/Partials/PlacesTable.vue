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
                    <button class="bg-brand-900 p-3 text-white border border-primary absolute" @click="onSearch"
                            dusk="search">
                        <i class="pi pi-search"></i>
                    </button>
                </div>
            </div>
          <div style="text-align: right">
            <create-place-form :locations="locations" :placeStatus="placeStatus" :placeTypes="placeTypes">
                        <jet-button
                            type="button"
                            class="ml-4 justify-end"
                            id="create_new_place"
                        >Create Place
                        </jet-button>
            </create-place-form>
          </div>
        </div>
          <div class="searchToolTip">
                <div v-if="searchValue"
                     class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-blue-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5>{{ searchValue }}</h5></div>
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
          :sortable="item.header !== 'Travel instructions'"
          :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }"
      >
        <div v-if="loading"><ProgressSpinner/></div>
        <template #body="slotProps">
            <div v-if="item.field==='name'">{{ slotProps.data[item.field] }}</div>
            <div v-if="item.field==='email'">{{ slotProps.data[item.field] }}</div>
            <div v-if="item.field==='place_type_name'">{{ slotProps.data[item.field] }}</div>
            <div v-if="item.field==='location_uuid'">
                    <jet-drop-down-widget
                        @change="onLocationChange($event, slotProps.data['uuid'])"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm h-8"
                        :items="locations"
                        :value="slotProps.data['location_uuid']"
                        :class="{'bg-green-300' : slotProps.data['location_uuid'], 'bg-red-200': !slotProps.data['location_uuid']}"
                    >
                    </jet-drop-down-widget>
            </div>
            <div v-if="item.field==='tsm_current_state'">
                    <div
                        v-if="slotProps.data.tsm_current_state === 'Active'"
                        class="text-white text-xs font-semibold px-1.5 py-1 rounded bg-green-500 mb-1 w-fit"
                    >
                        Active
                    </div>
                    <div v-if="slotProps.data.tsm_current_state === 'Inactive'"
                         class="text-white text-xs font-semibold px-1.5 py-1 rounded bg-red-600 mb-1 w-fit"
                    >
                        Inactive
                    </div>

                    <div v-if="slotProps.data.tsm_current_state === 'Error'"
                        class="text-gray-800 text-xs font-semibold px-1.5 py-1 rounded bg-yellow-400 mb-1 w-fit"
                    >
                        Error
                    </div>
                    <div v-if="slotProps.data.tsm_current_state === 'Archived'"
                         class="text-gray-800 text-xs font-semibold px-1.5 py-1 rounded bg-gray-200 mb-1 w-fit"
                    >
                        Archived
                    </div>
            </div>
        </template>
      </Column>

      <Column bodyStyle="text-align:right;" headerClass="align-right" field="actions" header="Actions"
              :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small', 'white-space': 'nowrap' }">
        <template #body="slotProps">
            <confirms-dialog
                v-if="slotProps.data.tsm_current_state !== 'Archived'"
                @confirmed="archivePlace(slotProps.data.uuid)"
                :buttonClass="'bg-red-500 hover:bg-red-800'"
                :title="'Warning!'"
                :content="
              'Are you sure you want to archive place <strong>' +
              slotProps.data.name +
              '</strong>? If you proceed you cannot undo this action and any pending Encounters will be impacted.'
            "
            >
                    <jet-button-small dusk="delete-button"
                                      class="
                justify-end
                hover:bg-red-700
                active:bg-red-900
                bg-red-500
              "
                    >
                        Archive
                    </jet-button-small>
                </confirms-dialog>
            <place-details :place="slotProps.data">
                    <jet-button-small
                        dusk="show_place_details"
                        id="show_place_details"
                        class="ml-2 justify-end bg-brand-300 hover:bg-brand-900 edit-user"
                    >Show Details
                    </jet-button-small>
                </place-details>
            <edit-place-form
                v-if="slotProps.data.tsm_current_state !== 'Archived'"
                :place="slotProps.data"
                :locations="locations"
                :placeTypes="placeTypes"
                    :placeStatus="placeStatus"
            >
              <jet-button-small dusk="edit-place" v-if="slotProps.data.tsm_current_state !== 'Error'"
                                class="ml-2 justify-end bg-brand-800 hover:bg-brand-900 edit-user"
              >
                        Edit
                    </jet-button-small>
            </edit-place-form>
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
import CreateLocationForm from "./CreateLocationForm.vue";
import {Link, useForm} from '@inertiajs/inertia-vue3';
import PlaceDetails from "./PlaceDetailsForm.vue";
import CreatePlaceForm from "./CreatePlaceForm.vue";
import EditPlaceForm from "./EditPlaceForm.vue";

export default defineComponent({

    props: {
        search: String,
        items: Array,
        paginationList: Array,
        advancedSearch: Array,
        tableData: Array,
        rowClass: Function,
        cellIcon: Function,
        locations: Array,
        userRoles: Array,
        errors: Object,
        message: String,
        timezones: Array,
        sortOrder: String,
        sortField: String,
        searchValue: String,
        placeTypes: Array,
        placeStatus: Array,
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
        CreateLocationForm,
        Link,
        PlaceDetails,
        CreatePlaceForm,
        EditPlaceForm

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
            loading: false,
            showSearch: false,
            searchText: this.search,
            selectedLocations: [],
            form: useForm({
                location_uuid: null,
            }),
        };
    },
    methods: {

        onSearch() {
            this.showAlert = false;
            this.showSearch = false;
            let payload = {search: this.searchText, sortField: this.sortField, sortOrder: this.sortOrder};

            this.showResult(payload)
        },

        onSort(event) {
            this.sortField = event.sortField
            this.sortOrder = event.sortOrder
            this.onSearch()
        },

        onLocationChange(event, uuid) {
            this.form.location_uuid = event.target.value;
            this.form.put(route('admin.resources.places.update', {uuid: uuid}), {
                preserveState: true,
            });
        },

        archivePlace(uuid) {
            this.form.delete(route('admin.resources.places.delete', {uuid: uuid}));
        },

        cancelSearch() {
            this.searchText = '';
            let payload = {search: '', sortField: this.sortField, sortOrder: this.sortOrder};
            this.showResult(payload)
        },

        showResult(payload) {
            this.$inertia.get(route('admin.resources.places.index'), payload, {
                preserveState: false,
            });
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

.p-datatable .p-datatable-tbody > tr:focus {
    outline: none;
}
</style>
