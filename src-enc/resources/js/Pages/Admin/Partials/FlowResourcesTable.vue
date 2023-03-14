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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div style="text-align: left">
                </div>
                <div style="text-align: right">
                    <add-resource-form :flow="flow">
                        <jet-button
                            type="button"
                            class="ml-4 justify-end"
                            id="add_place_flow"
                            dusk="add_place_flow"

                        >Add Place
                        </jet-button>
                    </add-resource-form>
                </div>
            </div>
        </template>
      <Column
          v-for="item of items"
          :rowEditor="true"
          :field="item.field"
          :header="item.header"
          :key="item.field"
          :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }"
      >
        <div v-if="loading"><ProgressSpinner/></div>
        <template #body="slotProps">
            <div v-if="item.field==='name'">{{ slotProps.data[item.field] }}</div>
            <div v-if="item.field==='type'">{{ slotProps.data[item.field][0] }}</div>
            <div v-if="item.field==='location'">{{ slotProps.data[item.field][0] }}</div>
            <div v-if="item.field==='is_active'">
                <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-green-300 w-fit"
                                                     v-if="slotProps.data[item.field]">Active
                </div>
                <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-red-300 w-fit"
                                                     v-else>Inactive
                </div>
            </div>


        </template>
      </Column>
        <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
                :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
            <template #body="slotProps">
                <div class="align-right">
                    <delete-flow-place :place="slotProps.data" :flow="flow">
                        <jet-button-small
                            dusk="delete-resources-button"
                            id="delete-resources-button"
                            class="justify-end
                hover:bg-red-700
                active:bg-red-900
                bg-red-500"
                        >Delete
                        </jet-button-small>
                    </delete-flow-place>
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
import CreateLocationForm from "./CreateLocationForm.vue";
import {Link} from '@inertiajs/inertia-vue3';
import AddResourceForm from "./AddResourceForm.vue";
import DeleteFlowPlace from "./DeleteFlowPlace.vue";

export default defineComponent({

    props: {
        flow: Object,
        search: String,
        items: Array,
        paginationList: Array,
        tableData: Array,
        rowClass: Function,
        cellIcon: Function,
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
        ProgressSpinner,
        CreateLocationForm,
        Link,
        AddResourceForm,
        DeleteFlowPlace,

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
            this.$inertia.put(route('admin.resources.places.update', {uuid: uuid}), {
                    location_uuid: event.target.value
                },
            );
        },

        cancelSearch() {
            this.searchText = '';
            let payload = {search: '', sortField: this.sortField, sortOrder: this.sortOrder};
            this.showResult(payload)
        },

        showResult(payload) {
            this.$inertia.get(route('admin.resources.places.index'), payload, {
                preserveState: true,
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
