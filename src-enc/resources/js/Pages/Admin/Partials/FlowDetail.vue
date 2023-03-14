<template>
    <app-layout title="Administration - Organisation - Flow Management">

        <template #header>
            <flow-header-component :flow="flow" :channelTypes="channelTypes"></flow-header-component>
        </template>
        <div class="py-12">
            <flow-component :flow="flow" :channelTypes="channelTypes"></flow-component>
            <div>
                <TabView v-model:activeIndex="activeTab" @tab-click="onTabClick">
                    <TabPanel header="Agents">
                        <app-table
                            :tableData="agents"
                            :rowClass="rowClass"
                            :flow="flow"
                            :errors="errors"
                            v-bind:items="[
                            { field: 'name', header: 'Name', type: 'text' },
                            { field: 'pool_name', header: 'Pool', type: 'text' },
                            { field: 'email', header: 'Email', type: 'text' },
                            { field: 'face_to_face', header: 'Face to Face', type: 'text' },
                            { field: 'web', header: 'Virtual', type: 'text' },
                            { field: 'phone', header: 'Phone', type: 'text' },
                        ]"
                        >
                        </app-table>
                    </TabPanel>

                    <TabPanel header="Places" v-if="flow.f2f"></TabPanel>
                    <TabPanel header="Encounters"></TabPanel>
                </TabView>

            </div>
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetLabel from "@/Components/Label.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import '@dafcoe/vue-collapsible-panel/dist/vue-collapsible-panel.css'
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import AppTable from './FlowAgentsTable.vue';
import FlowComponent from '@/Components/FlowComponent.vue';
import FlowHeaderComponent from '@/Components/FlowHeaderComponent.vue';

export default defineComponent({
    props: {
        flow: Object,
        agents: Object,
        channelTypes: Object,
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
                this.$inertia.visit(route('admin.flows.resources', {uuid: this.flow.uuid}));
            } else if (event.index === 2) {
                let payload = {
                    search: this.searchText,
                    sortField: this.sortField,
                    sortOrder: this.sortOrder,
                    flows: this.searchFlows,
                    agent: this.searchAgents,
                    place: this.searchPlaces,
                    location: this.searchLocations
                };
                this.$inertia.visit(route('admin.flows.encounters', {uuid: this.flow.uuid}), payload, {
                    preserveState: true,
                });
            }

        }

    },

    data() {
        return {
            activeTab: 0,
        };
    },
});

</script>

<style>
.p-tabview .p-tabview-nav {
    background: transparent !important;
}
</style>
