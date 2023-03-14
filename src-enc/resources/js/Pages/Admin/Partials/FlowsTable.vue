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
            <div style="text-align: right">
                <add-flow-form :channelTypes="channelTypes">
                        <jet-button
                            type="button"
                            class="ml-4 justify-end"
                            id="add_flow"
                            dusk="add_flow"

                        >Add flow
                        </jet-button>
                    </add-flow-form>
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
          :sortable="item.header !== 'Current Places'"
          :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }"
      >
        <div v-if="loading"><ProgressSpinner/></div>

        <template #body="slotProps">
            <div>{{ slotProps.data[item.field] }}</div>
        </template>
      </Column>
        <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
                :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
            <template #body="slotProps">
                    <jet-button-small
                        @click="view( slotProps.data.uuid )"
                        dusk="view_flow"
                        id="view_flow"
                        class="ml-2 justify-end bg-brand-800 hover:bg-brand-900 edit-user"
                    >View
                    </jet-button-small>

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
import {Link} from '@inertiajs/inertia-vue3';
import AddFlowForm from "./AddFlowForm.vue";

export default defineComponent({

    props: {
        search: String,
        items: Array,
        paginationList: Array,
        tableData: Array,
        rowClass: Function,
        cellIcon: Function,
        listForSelect: Array,
        userRoles: Array,
        errors: Object,
        message: String,
        sortOrder: String,
        sortField: String,
        searchValue: String,
        channelTypes: Object,
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
        Link,
        AddFlowForm,

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
            };
            this.showResult(payload)

        },

        cancelSearch() {
            this.searchText = ''
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
            };
            this.showResult(payload)
        },

        showResult(payload) {
            this.$inertia.get(route('admin.flows.index'), payload, {
                preserveState: true,
            });
        },

        view(uuid) {
            this.$inertia.visit(route('admin.flows.agents', uuid));
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
