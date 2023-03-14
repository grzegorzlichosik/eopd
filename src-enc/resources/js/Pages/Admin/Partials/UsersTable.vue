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
                    <div  class="relative w-min">
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
                            <jet-label class="mt-2 text-gray-600">Email Verified</jet-label>

                            <jet-drop-down-widget class="w-full" v-model="email_verified_at" :items="emailStatus"
                                                  name="email_verified_at"/>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <jet-label class="mt-2 text-gray-600">Role</jet-label>
                            <jet-drop-down-widget class="w-full" v-model="role" :items="roles"
                                                  name="role"/>
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
                <div style="text-align: right">
                    <create-user-form :roles="listForSelect">
                        <jet-button
                            type="button"
                            class="ml-4 justify-end"
                            id="invite_new_user"
                        >Invite new user
                        </jet-button>
                    </create-user-form>
                </div>
            </div>
            <div class="searchToolTip">
                <div v-if="searchFilters['searchValue']"  class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-blue-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5>{{ searchFilters['searchValue'] }}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelSearch" class="searchClose">
                            <i class="fa fa-close"></i>
                        </button></div>
                    </div>
                </div>
                <div v-if="searchFilters['email_verified_at'] === '1'" class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-gray-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5> Email verified: Verified</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelEmail" class="searchClose" >
                            <i class="fa fa-close"></i>
                        </button>
                        </div>
                    </div>
                </div>
                <div v-if="searchFilters['email_verified_at'] === '-1'" class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-gray-300 mb-1 w-fit pill">
                    <div class="searchTool">
                        <div><h5> Email verified: Pending Verification</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelEmail" class="searchClose">
                            <i class="fa fa-close"></i>
                        </button></div>
                    </div>
                </div>
                <div v-if="searchFilters['role']"  class="text-gray-600 text-sm font-semibold px-1.5 py-1 rounded bg-orange-300 mb-1 w-fit pill" >
                    <div class="searchTool">
                        <div><h5>Role: {{ searchFilters['role'] }}</h5></div>
                        <div style="margin-left:10px"> <button @click="cancelRole" class="searchClose">
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
            :sortable="(item.header !== 'Roles') &&  (item.header !== 'Pools') && (item.header !== 'Upcoming meetings')"
            :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }"
        >

            <template #body="slotProps">
                <div v-if="item.field=='name'">{{ slotProps.data[item.field] }}</div>
                <div v-if="item.field=='email'">{{ slotProps.data[item.field] }}</div>
                <div v-if="item.field=='email_verified_at'">
                    <span v-if="slotProps.data[item.field]" class="text-light-green">
                        Verified
                    </span>
                    <span v-else class="text-app-orange">
                        Pending Verification
                    </span>
                </div>
                <div v-if="item.field==='role_name'">
                    <span v-if="slotProps.data.is_super_admin">
                        <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-blue-300 mb-1 w-fit">Super&nbsp;Administrator</div>
                    </span>
                    <span v-if="slotProps.data.is_admin">
                        <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-green-300 mb-1 w-fit">Administrator</div>
                    </span>
                    <span v-if="slotProps.data.is_agent">
                        <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-yellow-300 mb-1 w-fit">Agent</div>
                    </span>
                    <div v-if="slotProps.data.is_developer"
                         class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-lime-300 w-fit">Developer
                    </div>
                </div>
                <div v-if="item.field === 'last_login_at'">
                    {{
                        slotProps.data[item.field]
                            ?
                            toLocalDate(slotProps.data[item.field], $page.props.default_date_format)
                            + ' ' +
                            toLocalTime(slotProps.data[item.field])
                            : ""
                    }}
                </div>
                <div v-if="item.field === 'pools'">

                    <div v-for="item in slotProps.data['pools'].slice(0,3)">
                        <div class="bg-gray-300 text-xs font-semibold px-1.5 py-1 rounded mb-1 w-fit">
                            {{ item }}
                        </div>
                    </div>


                    <div v-if="slotProps.data['pools'].length>3">

                        <div v-if="viewMoreActivated" @click="activateViewLess">
                            <div v-for="item in slotProps.data['pools'].slice(3,slotProps.data['pools'].length)">
                                <div class="bg-gray-300 text-xs font-semibold px-1.5 py-1 rounded mb-1 w-fit">
                                    {{ item }}
                                </div>

                            </div>
                            <div v-if="slotProps.data['pools'].length>3" class="underline">
                                View less
                            </div>
                        </div>

                        <div v-else @click="activateViewMore" class="underline">
                            View more
                        </div>

                    </div>
                </div>
                <div v-if="item.field=='upcoming_meetings'">{{ slotProps.data[item.field] }}</div>

            </template>
        </Column>
        <Column bodyStyle="text-align:right" headerClass="align-right" field="actions" header="Actions"
                :style="{ 'max-width': 'auto', 'text-overflow': 'ellipsis','font-size':'small' }">
            <template #body="slotProps">

                <delete-user-form :user="slotProps.data">
                    <jet-button-small
                        v-if="showDeleteButton(slotProps.data)"
                        dusk="delete-user-button"
                        class="justify-end
                hover:bg-red-700
                active:bg-red-900
                bg-red-500"
                    >Delete
                    </jet-button-small>
                </delete-user-form>

                <update-user-form :user="slotProps.data">
                    <jet-button-small
                        dusk="edit-user-button"
                        class="ml-2 justify-end bg-brand-800 hover:bg-brand-900 edit-user"
                    >Edit
                    </jet-button-small>
                </update-user-form>

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
import CreateUserForm from "./CreateUserForm.vue";
import UpdateUserForm from "./UpdateUserForm.vue";
import DeleteUserForm from "./DeleteUserForm.vue";

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
        searchFilters: Array,
        roles: Array,
        emailStatus: Array,
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
        CreateUserForm,
        UpdateUserForm,
        DeleteUserForm,
    },
    methods: {
        onSearch() {
            this.showAlert = false;
            this.showSearch = false;
            let payload = {
                search: this.searchText,
                sortField: this.sortField,
                sortOrder: this.sortOrder,
                email_verified_at: this.email_verified_at,
                role: this.role
            };

            console.log(payload,"hi");
            this.showResult(payload)
        },

        onSort(event) {
            this.sortField = event.sortField
            this.sortOrder = event.sortOrder
            let payload = {
                search: this.searchFilters['searchValue'],
                email_verified_at: this.searchFilters['email_verified_at'],
                role: this.searchFilters['role'],
                sortField: this.sortField,
                sortOrder: this.sortOrder
            };
            this.showResult(payload)
        },

        showSearchModal() {
            this.showSearch = true;
        },

        closeSearchModal() {
            this.showSearch = false;
        },

        showDeleteButton(user) {
            return !(user.active_user || (user.is_super_admin && !this.$page.props.user.is_super_admin));
        },
        activateViewMore() {
            this.viewMoreActivated = true;
        },
        activateViewLess() {
            this.viewMoreActivated = false;
        },
        cancelSearch() {
            this.searchText = '';
            let payload = {
                search: '',
                email_verified_at: this.searchFilters['email_verified_at'],
                role: this.searchFilters['role'],
                sortField: this.sortField,
                sortOrder: this.sortOrder
            };
            this.showResult(payload)
        },

        cancelEmail() {
            this.searchText = '';
            let payload = {
                search: this.searchFilters['searchValue'],
                email_verified_at: '',
                role: this.searchFilters['role'],
                sortField: this.sortField,
                sortOrder: this.sortOrder
            };
            this.email_verified_at = null
            this.showResult(payload)
        },

        cancelRole() {
            this.searchText = '';
            let payload = {
                search: this.searchFilters['searchValue'],
                email_verified_at: this.searchFilters['email_verified_at'],
                role: '',
                sortField: this.sortField,
                sortOrder: this.sortOrder
            };
            this.role = null
            this.showResult(payload)
        },
        showResult(payload) {
            this.$inertia.get(route('admin.users'), payload, {
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
            editingRows: [],
            selected: "",
            deactivated: false,
            showSearch: false,
            viewMoreActivated: false,
            searchText: this.searchFilters['searchValue'],
            email_verified_at: this.searchFilters['email_verified_at'],
            role: this.searchFilters['role'],
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
