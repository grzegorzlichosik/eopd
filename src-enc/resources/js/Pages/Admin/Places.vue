<template>
    <app-layout title="Administration - Organisation - Pools">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="doh-users-header">
                Locations & Places
            </h2>
        </template>

        <div class="py-12">
            <TabView v-model:activeIndex="activeTab" @tab-click="onTabClick">
                <TabPanel header="Locations">
                </TabPanel>
                <TabPanel header="Places">
                    <app-table
                        :paginationList="places.links"
                        :tableData="list"
                        :rowClass="rowClass"
                        :timezones="timezones"
                        :locations="locations"
                        :placeTypes="placeTypes"
                        :placeStatus="placeStatus"
                        :searchValue="searchValue"
                        :errors="errors"
                        v-bind:items="[
                            { field: 'name', header: 'Name', type: 'text' },
                            { field: 'email', header: 'Email', type: 'text' },
                            { field: 'tsm_current_state', header: 'Status', type: 'text' },
                            { field: 'place_type_name', header: 'Type', type: 'text' },
                            { field: 'location_uuid', header: 'Location', type: 'text' },
                        ]"
                    >
                    </app-table>
                </TabPanel>
            </TabView>

        </div>
    </app-layout>
</template>

<script>
import {computed, defineComponent, ref} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Components/Button.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import JetInput from "@/Components/Input.vue";
import JetLabel from "@/Components/Label.vue";
import AppTable from "./Partials/PlacesTable.vue";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import {usePage} from "@inertiajs/inertia-vue3";
import { Inertia } from '@inertiajs/inertia'

export default defineComponent({
    props: {
        errors: Object,
        locations: Object,
        places: Object,
        timezones: Array,
        searchValue: String,
        placeTypes: Array,
        placeStatus: Array,
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
        TabPanel,
        Inertia
    },
    setup(props) {
        const list = computed(() => props.places.data);
        const activeTab = ref(1);

        const onTabClick = (event) => {
            if (event.index === 0) {
                Inertia.visit(route('admin.resources.locations.index'));
            }
        }

        const rowClass = (data) =>{
            return null;
        }

        return {
            activeTab,
            onTabClick,
            rowClass,
            list
        }

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


