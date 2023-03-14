<template>

    <app-layout title="Administration - Organisation - Schedule Management">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="schedule-management-header">
                My Schedule
            </h2>
        </template>
        <div class="py-12">
            <TabView v-model:activeIndex="activeTab" @tab-click="onTabClick">
                <TabPanel header="Encounters">
                    <app-table :paginationList="encounters.links" :tableData="encounters.data" :rowClass="rowClass"
                               :errors="errors" :flowsList="flows" :placesList="places"
                               :locationsList="locations" :channelList="channelTypes" :statusList="status"
                               :searchFilter="searchFilter" v-bind:items="[
                            { field: 'scheduled_at', header: 'Date/ Time', type: 'text' },
                            { field: 'flow_name', header: 'Flow Name', type: 'text' },
                            { field: 'channel_types_name', header: 'Channel', type: 'text' },
                            { field: 'place_name', header: 'Place', type: 'text' },
                            { field: 'main_attendee', header: 'Requestor', type: 'text' },
                            { field: 'attendees_count', header: 'Attendees', type: 'text' },
                            { field: 'tsm_current_state', header: 'Status', type: 'text' },
                        ]">
                    </app-table>
                </TabPanel>
                <TabPanel header="Calendar View">
                </TabPanel>
            </TabView>

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
import AppTable from "./Partials/EncountersTable.vue";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';

export default defineComponent({
    props: {
        errors: Object,
        encounters: Object,
        flows: Array,
        agents: Array,
        places: Array,
        locations: Array,
        channelTypes: Array,
        searchFilter: Array,
        status: Array,
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
                this.$inertia.visit(route('agent.encounters.all'));
            } else if (event.index === 1) {
                this.$inertia.visit(route('agent.encounters.calendar'));
            }
        }
    },
    data() {
        return {
            activeTab: 0
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


