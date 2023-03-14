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
               <AddUpdateLocationForm :timezones="timezones" :preferredCountries="preferredCountries">
                          <jet-button
                              type="button"
                              class="ml-4 justify-end"
                              id="create_location"
                              dusk="create_location"
                              @click="clearLocation"
                          >Create Location
                          </jet-button>
               </AddUpdateLocationForm>
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
              <div v-if="item.field==='timezone'">{{ slotProps.data[item.field] }}</div>
              <div v-if="item.field==='phone'">{{ slotProps.data[item.field] }}</div>
              <div v-if="item.field==='places_count'">{{ slotProps.data[item.field] }}</div>
              <div v-if="item.field==='file'">
                  <span v-if="slotProps.data[item.field]">
                  <a :href="route('admin.resources.locations.instructions',[slotProps.data[item.field]['uuid']])"
                     class="
                     shadow text-secondary inline-flex items-center px-2 py-1 border border-transparent rounded-md
                     font-semibold text-xs text-white tracking-widest focus:outline-none focus:border-gray-900 focus:ring
                     focus:ring-gray-300 disabled:opacity-25 transition ml-2 justify-end bg-green-500 hover:bg-green-600"
                  >
                      <i class="fa fa-download mr-2"></i>Download
                      </a>
                  </span>
              </div>
          </template>
        </Column>

        <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
                :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
          <template #body="slotProps">
              <update-file :uuid="slotProps.data.uuid">
                  <jet-button-small dusk="upload_file" id="upload_file"
                                  class="ml-2 justify-end bg-brand-300 hover:bg-brand-900 edit-user"
                >
                      <i class="fa fa-upload"></i>Upload
                  </jet-button-small>
              </update-file>
              <AddUpdateLocationForm :timezones="timezones" :location="slotProps.data" :preferredCountries="preferredCountries">
                  <jet-button-small dusk="edit_location" id="edit_location"
                                    class="ml-2 justify-end bg-brand-800 hover:bg-brand-900 edit-user"
                                    @click="updateLocation(slotProps.data.uuid)"
                  >
                      Edit
                  </jet-button-small>

              </AddUpdateLocationForm>

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
import AddUpdateLocationForm from "./AddUpdateLocationForm ";
import {Link} from '@inertiajs/inertia-vue3';
import { useLocationStore } from "../../../../../store/location/locationStore";
import UpdateFile from './LocationFileForm';

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
        timezones: Array,
        sortOrder: String,
        sortField: String,
        preferredCountries: Array,
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
        useLocationStore,
        AddUpdateLocationForm,
        UpdateFile,
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
            locations:useLocationStore(),
            showSearch: false,
            searchText: this.search,
            upadate_location_data: null,
        };
    },
    methods: {

        onSearch() {
            this.showAlert = false;
            this.showSearch = false;
            let payload = {search: this.searchText};

            this.$inertia.get(route('admin.resources.locations.index'), payload, {
                preserveState: true,
            });
        },

        onSort(event) {
            this.sortField = event.sortField
            this.sortOrder = event.sortOrder
            let payload = {sortField: this.sortField, sortOrder: this.sortOrder};
            this.$inertia.get(route('admin.resources.locations.index'), payload, {
                preserveState: true,
            });
        },

        downloadPdf(uuid) {
            this.$inertia.get(route('admin.resources.locations.instructions', uuid), null, {
                preserveState: true,
            });
        },

        updateLocation(e){
            let get_location =this?.locations?.entireLocation?.find((val)=>{return val?.uuid === e})
            this?.locations?.$patch({
                updated_location:get_location,
                input_address:true,
                gmap_address:false
            })

        },
        clearLocation(){
            this?.locations?.$patch({
                updated_location:null,
                input_address:false,
                gmap_address:true,
                is_locationEnable:false
            })
        }

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
