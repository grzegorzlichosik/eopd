<template>

    <app-layout title="Administration - Organisation - Flow Management">
        <template #header>
            <flow-header-component :flow="flow" :channelTypes="channelTypes"></flow-header-component>
        </template>
        <div class="py-12">
            <flow-component :flow="flow" :channelTypes="channelTypes"></flow-component>
            <div>
                <TabView v-model:activeIndex="activeTab" @tab-click="onTabClick">
                    <TabPanel header="Agents"></TabPanel>
                    <TabPanel header="Places" v-if="flow.f2f"></TabPanel>
                    <TabPanel header="Encounters">
                        <app-table
                            :paginationList="encounters.links"
                            :tableData="encounters.data"
                            :rowClass="rowClass"
                            :errors="errors"
                            :flowUuid="flow.uuid"
                            :agentsList="agents"
                            :placesList="places"
                            :locationsList="locations"
                            :statusList="status"
                            :channelList="channelTypes"
                            :searchFilter="searchFilter"
                            v-bind:items="[
                            { field: 'uuid', header: 'Uuid', type: 'text' },
                            { field: 'channel_types_name', header: 'Channel', type: 'text'},
                            { field: 'agent_name', header: 'Agent', type: 'text' },
                            { field: 'main_attendee', header: 'Requestor', type: 'text' },
                            { field: 'attendees_count', header: 'Attendees', type: 'text' },
                            { field: 'place_name', header: 'Place', type: 'text' },
                            { field: 'scheduled_at', header: 'Scheduled at (your timezone)', type: 'text' },
                            { field: 'tsm_current_state', header: 'Status', type: 'text' },
                        ]"
                        >
                        </app-table>
                    </TabPanel>
                </TabView>
            </div>
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import {Inertia} from "@inertiajs/inertia";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetLabel from "@/Components/Label.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import '@dafcoe/vue-collapsible-panel/dist/vue-collapsible-panel.css'
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import AppTable from './FlowEncountersTable.vue';
import FlowComponent from '@/Components/FlowComponent.vue';
import FlowHeaderComponent from '@/Components/FlowHeaderComponent.vue';

export default defineComponent({
    props: {
        flow: Object,
        channelTypes: Array,
        encounters: Object,
        agents: Array,
        places: Array,
        locations: Array,
        searchFilter: Array,
        status: Array,
    },

    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
        JetLabel,
        JetButtonSmall,
        TabView,
        TabPanel,
        AppTable,
        FlowComponent,
        FlowHeaderComponent,
    },

    methods: {

        view(uuid) {
            let payload = {
                flows: this.flow.uuid,
            };
            this.$inertia.get(route('admin.encounters.index'), payload, {
                preserveState: true,
            });
        },
        onTabClick(event) {
            if (event.index === 1) {
                this.$inertia.visit(route('admin.flows.resources', {uuid : this.flow.uuid}));
            }
            else if (event.index === 0) {
                this.$inertia.visit(route('admin.flows.agents', {uuid : this.flow.uuid}));
            }

        }

    },

    data() {
        return {
            activeTab: 2,
        };
    },



});

</script>

<style>
.p-tabview .p-tabview-nav {
    background: transparent !important;
}
</style>
