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
              <add-user-form :pool="pool" selectedUserValue="selected">
                <jet-button
                    type="button"
                    class=" ml-2
                    justify-end
                    bg-brand-400
                    hover:bg-brand-600
                    active:bg-brand-600
                    whitespace-nowrap justify-end"
                    id="user"
                >
                    Invite New Users
                </jet-button>
              </add-user-form>
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
        <div v-if="loading"><ProgressSpinner/></div>
        <template #body="slotProps">
            <div v-if="item.field=='name'">{{ slotProps.data[item.field] }}</div>
            <div v-if="item.field=='email'">{{ slotProps.data[item.field] }}</div>

        </template>
      </Column>
      <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
              :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
        <template #body="slotProps">

             <confirms-dialog
                 @confirmed="deleteUsers(slotProps.data.uuid)"
                 :title="'Remove Member: ' + slotProps.data.email"
                 :content="
              'Are you sure you want to remove user <strong>' +
              slotProps.data.name +
              '</strong> from pool?'
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
                        Remove user from the pool
                    </jet-button-small>
                </confirms-dialog>

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
import AddUserForm from "./AddUserForm.vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default defineComponent({

    props: {
        pool: Object,
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
        sortField: String
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
        AddUserForm
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
        deleteUsers(user_uuid) {
            this.$inertia.delete(route('admin.pools.users.delete', {uuid: this.pool.uuid, user_uuid: user_uuid}));
        },

        onSort(event) {
            this.sortField = event.sortField
            this.sortOrder = event.sortOrder
            let payload = { sortField: this.sortField, sortOrder: this.sortOrder };
            this.$inertia.get(route('admin.pools.show',{uuid: this.pool.uuid}), payload, {
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
</style>
