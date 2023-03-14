<template>
    <app-layout title="Platform - Organisations">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="doh-users-header">
                Platform - Organisations
            </h2>
        </template>
        <app-table
            :paginationList="organisations.links"
            :tableData="organisations.data"
            :rowClass="rowClass"
            :searchValue="searchValue"
            :errors="errors"
            v-bind:items="[
            { field: 'name', header: 'Name', type: 'text' },
            { field: 'phone_number', header: 'Phone Number', type: 'text' },
            { field: 'created_by', header: 'Organisation created by', type: 'text' },
            { field: 'created_at', header: 'Created at', type: 'text' },
            { field: 'creator_status', header: 'Creator Status', type: 'text' },
            { field: 'users_count', header: 'No. users', type: 'text' },
            { field: 'master_calendar_email', header: 'Master Calendar Status', type: 'text' },
            { field: 'master_calendar_nylas_account_id', header: 'Nylas Status', type: 'text' },
          ]"
            v-bind:advancedSearch="[]"
        >
        </app-table>
    </app-layout>
</template>

<script>
import {defineComponent, reactive, onMounted} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import AppTable from "./Partials/OrganisationsTable.vue";

export default defineComponent({
    props: {
        organisations: Object,
        errors: Object,
        searchValue: String,
    },

    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
        AppTable,
    },
    setup(props) {
        return {}
    }
});
</script>


