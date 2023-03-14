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
                    <app-table
                        :paginationList="locations.links"
                        :listForSelect="roleList"
                        :tableData="locations.data"
                        :rowClass="rowClass"
                        :timezones="timezones"
                        :errors="errors"
                        :preferredCountries="preferredCountries"
                        v-bind:items="[
                            { field: 'name', header: 'Name', type: 'text' },
                            { field: 'timezone', header: 'Timezone', type: 'text' },
                            { field: 'phone', header: 'Phone', type: 'text' },
                            { field: 'places_count', header: 'Places', type: 'text' },
                            { field: 'file', header: 'Travel instructions', type: 'text' },
                        ]"
                    >
                    </app-table>
                </TabPanel>
                <TabPanel header="Places">
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
import AppTable from "./Partials/LocationTable.vue";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import { useLocationStore } from "../../../../store/location/locationStore";

export default defineComponent({
    props: {
        errors: Object,
        locations: Object,
        timezones: Array,
        preferredCountries: Array,
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
        onTabClick(event){
            if(event.index === 1){
                this.$inertia.visit(route('admin.resources.places.index'));
            }
        }
    },
    data() {
        return {
            creating: false,
            form: this.$inertia.form({
                name: "",
                selectedUsers: null,
            }),
            activeTab: 0,
        };
    },
    mounted(){
        let locationStore = useLocationStore()
        locationStore?.$patch({
            entireLocation : this?.locations?.data
        });
    },
    watch: {
        locations(newQuestion) {
            let locationStore = useLocationStore()
            locationStore?.$patch({
                entireLocation : newQuestion?.data
            });
        }
    },


});

</script>
<style>
.p-tabview .p-tabview-nav{
    background: transparent;
}
.p-tabview .p-tabview-nav li .p-tabview-nav-link{
    background: #efefef;
}
.p-tabview-header.p-highlight a{
    background: #ffffff;
}
</style>
