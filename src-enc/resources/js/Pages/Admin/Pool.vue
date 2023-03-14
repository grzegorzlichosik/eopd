<template>
    <app-layout title="Administration - Organisation - Pools">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="pool-header">
                Pool: {{ pool.name }}
                <update-pool-form :pool="pool">
                    <jet-button-small
                        type="button"
                        class="ml-4 justify-end bg-brand-300"
                        id="edit_new_pool"
                    >EDIT
                    </jet-button-small>
                </update-pool-form>
            </h2>
        </template>

        <div class="py-12">
            <TabView>
                <TabPanel header="Users">
                    <app-table
                        :pool="pool"
                        :paginationList="pool.users.links"
                        :tableData="pool.users.data"
                        :rowClass="rowClass"
                        :errors="errors"
                        v-bind:items="[
                            { field: 'name', header: 'Name', type: 'text' },
                            { field: 'email', header: 'Email', type: 'text' },
                        ]"
                    >
                    </app-table>
                </TabPanel>
            </TabView>

        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Components/Button.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import JetInput from "@/Components/Input.vue";
import JetLabel from "@/Components/Label.vue";
import AppTable from "./Partials/ManagePoolTable.vue";
import UpdatePoolForm from "./Partials/EditPool.vue";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';

export default defineComponent({
    props: {
        errors: Object,
        pool: Object,
    },

    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
        UpdatePoolForm,
        JetButton,
        JetButtonSmall,
        JetInput,
        JetLabel,
        AppTable,
        TabView,
        TabPanel
    },
    methods: {
        rowClass(data) {
            return null;
        },
    },
    data() {
        return {
            creating: false,
            form: this.$inertia.form({
                name: "",
                selectedUsers: null,
            }),
        };
    },

});

</script>
<style>
.p-tabview .p-tabview-nav{
    background: rgb(229 231 235);
}
</style>


