<template>

    <app-layout title="Administration - Organisation - Flow Management">
        <template #header>
            <flow-header-component :flow="flow" :channelTypes="channelTypes"></flow-header-component>
        </template>
        <div class="py-12">

            <flow-component :flow="flow" :channelTypes="channelTypes"></flow-component>
            <div>
                <TabView v-model:activeIndex="activeTab" @tab-click="onTabClick">
                    <TabPanel header="Agents" >

                    </TabPanel>
                    <TabPanel header="Places">
                        <app-table
                            :tableData="resources"
                            :flow="flow"
                            v-bind:items="[
                            { field: 'name', header: 'Name', type: 'text' },
                            { field: 'type', header: 'Type', type: 'text' },
                            { field: 'location', header: 'Location', type: 'text' },
                            { field: 'is_active', header: 'Status', type: 'text' },
                        ]"
                        >
                        </app-table>

                    </TabPanel>
                    <TabPanel header="Encounters">

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
import AppTable from './FlowResourcesTable.vue';
import FlowComponent from '@/Components/FlowComponent.vue';
import FlowHeaderComponent from '@/Components/FlowHeaderComponent.vue';

export default defineComponent({
    props: {
        flow: Object,
        channelTypes: Object,
        resources: Object,
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
            if (event.index === 0) {
                this.$inertia.visit(route('admin.flows.agents', {uuid : this.flow.uuid}));
            }
            else if (event.index === 2) {
                this.$inertia.visit(route('admin.flows.encounters', {uuid : this.flow.uuid}));
            }
        }

    },

    data() {
        return {
            activeTab: 1,
        };
    },



});

</script>

<style>
.p-tabview .p-tabview-nav {
    background: transparent !important;
}
</style>
