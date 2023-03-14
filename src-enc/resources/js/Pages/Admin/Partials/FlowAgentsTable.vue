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
                    <add-agent-form :flow="flow">
                        <jet-button
                            type="button"
                            class="ml-4 justify-end"
                            id="add_agent_flow"
                            dusk="add_agent_flow"

                        >Add agent
                        </jet-button>
                    </add-agent-form>
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
            <div v-if="item.field==='email'">{{ slotProps.data[item.field] }}</div>
            <div v-if="item.field==='pool_name'">{{ slotProps.data[item.field] }}</div>
            <div v-if="(item.field==='face_to_face')">
                <jet-checkbox
                    name="face"
                    @click="onChannelChange($event, slotProps.data, 'face_to_face')"
                    :checked="slotProps.data[item.field]"
                    :disabled="!flow.f2f"
                    :class="{'opacity-25': !flow.f2f}"
                />
             </div>
             <div v-if="(item.field==='web')">
                 <jet-checkbox
                     name="web"
                     @click="onChannelChange($event, slotProps.data, 'web')"
                     :checked="slotProps.data[item.field]"
                     :disabled="!flow.web"
                     :class="{'opacity-25': !flow.web}"
                 />
             </div>
             <div v-if="(item.field==='phone')">
                 <jet-checkbox
                     name="phone"
                     @click="onChannelChange($event, slotProps.data, 'phone')"
                     :checked="slotProps.data[item.field]"
                     :disabled="!flow.phone"
                     :class="{'opacity-25': !flow.phone}"
                 />
             </div>
        </template>
      </Column>
         <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
                 :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
            <template #body="slotProps">
                <div style="display: flex;flex-direction: row;justify-content: right">
                     <delete-flow-agent :agent="slotProps.data">
                        <jet-button-small
                            dusk="delete-agents-button"
                            id="delete-agents"
                            class="justify-end
                hover:bg-red-700
                active:bg-red-900
                bg-red-500"
                        >Delete
                        </jet-button-small>
                     </delete-flow-agent>
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
import AddAgentForm from "./AddAgentForm";
import DeleteFlowAgent from "./DeleteFlowAgent.vue";


export default defineComponent({

    props: {
        flow: Object,
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
        AddAgentForm,
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
        DeleteFlowAgent,

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
        };
    },
    methods: {
        onChannelChange(event, data, type) {
            let routeName = event.target.checked
                ? 'admin.flows.agent.addChannel'
                : 'admin.flows.agent.removeChannel';

            this.$inertia.put(route(routeName, {uuid: this.flow.uuid}), {
                    checkedStatus: event.target.checked,
                    type: type,
                    email: data.email,
                },
            );
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
