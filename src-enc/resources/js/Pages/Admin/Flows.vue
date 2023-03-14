<template>

    <app-layout title="Administration - Organisation - Flow Management">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="doh-users-header">
                Flow Management
            </h2>
        </template>

        <div class="py-12">
            <app-table
                :paginationList="flows.links"
                :tableData="flows.data"
                :channelTypes="channelTypes"
                :rowClass="rowClass"
                :searchValue="searchValue"
                :errors="errors"
                v-bind:items="[
                            { field: 'name', header: 'Name', type: 'text' },
                            { field: 'channels_count', header: 'Channels', type: 'text' },
                            { field: 'users_count', header: 'Agents', type: 'text' },
                            { field: 'encounters_count', header: 'Total Encounters', type: 'text' },
                            { field: 'places_count', header: 'Current Places', type: 'text' },
                        ]"
            >
            </app-table>

        </div>
    </app-layout>
</template>

<script>
import {defineComponent, onMounted, reactive} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Components/Button.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import JetInput from "@/Components/Input.vue";
import JetLabel from "@/Components/Label.vue";
import AppTable from "./Partials/FlowsTable.vue";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';

export default defineComponent({
    props: {
        errors: Object,
        flows: Object,
        channelTypes: Object,
        searchValue: String,
    },

    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
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
        onTabClick(event) {
            if (event.index === 0) {
                this.$inertia.visit(route('admin.resources.locations.index'));
            }
        }
    },

    data() {
        return {
            activeTab: 1,
            selectedLocations: [],
        };
    },

});

</script>
<style>
.p-tabview .p-tabview-nav {
    background: transparent;
}

.p-tabview .p-tabview-nav li .p-tabview-nav-link {
    background: #efefef;
}

.p-tabview-header.p-highlight a {
    background: #ffffff;
}
</style>

