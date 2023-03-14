<template>
    <app-layout title="Administration - Organisation - Pools">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="doh-users-header">
                Administration - Pools
            </h2>
        </template>
        <div class="container mx-auto my-10">
            <app-table
                :paginationList="pools.links"
                :tableData="pools.data"
                :errors="errors"
                v-bind:items="[
            { field: 'name', header: 'Name', type: 'text' },

          ]"

            >
            </app-table>
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import AppTable from "./Partials/PoolTable.vue";

export default defineComponent({
    props: {
        pools: Object,
        errors: Object,
    },

    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
        AppTable,
    },
    methods: {
        rowClass(data) {
            return null;
        },
    },

});
</script>
