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
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">

          <div style="text-align: right">
                    <create-pool-form>
                        <jet-button
                            type="button"
                            class="ml-4 justify-end"
                            id="create_new_pool"
                        >CREATE NEW POOL
                        </jet-button>
                    </create-pool-form>
                </div>
        </div>
      </template>
      <Column
          v-for="item of items"
          :rowEditor="true"
          :field="item.field"
          :header="item.header"
          :key="item.field"
          :sortable="item.header !== ''"
          :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }"
      >

        <template #body="slotProps">
            <div v-if="item.field=='name'">{{ slotProps.data[item.field] }}</div>

        </template>
      </Column>
      <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
              :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
        <template #body="slotProps">

            <jet-button-small
                class="ml-2 justify-end bg-brand-800 hover:bg-brand-900 edit-pool" @click="view( slotProps.data.uuid )"
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
import CreatePoolForm from "./CreatePool.vue";


export default defineComponent({
    props: {
        search: String,
        items: Array,
        paginationList: Array,
        advancedSearch: Array,
        tableData: Array,
        errors: Object,
        sortOrder: String,
        sortField: String,
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
        CreatePoolForm,

    },
    methods: {
        view(uuid) {
            this.$inertia.get(route('admin.pools.show', {uuid: uuid}));
        },

        onSort(event) {
            this.sortField = event.sortField
            this.sortOrder = event.sortOrder
            let payload = { sortField: this.sortField, sortOrder: this.sortOrder };
            this.$inertia.get(route('admin.pools'), payload, {
                preserveState: true,
            });
        },
    },

    data() {
        return {
            sortField: '',
            sortOrder: '',
            searchText: this.search,
        };
    },
});
</script>

<style>
.align-right{
    text-align:right !important;
}
.align-center{
    text-align:center !important;
}
.p-datatable th[class*="align-"] .p-column-header-content {
    display: inline-flex ;
}
</style>
