<template>
    <app-layout title="Administration - Users">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="doh-users-header">
                Administration - Users
            </h2>
        </template>
        <app-table
            :paginationList="users.links"
            :listForSelect="roleList"
            :tableData="users.data"
            :rowClass="rowClass"
            :searchFilters="searchFilters"
            :roles="roles"
            :emailStatus="emailStatus"
            :errors="errors"
            v-bind:items="[
            { field: 'name', header: 'Name', type: 'text' },
            { field: 'email', header: 'Email', type: 'text' },
            {
              field: 'email_verified_at',
              header: 'Email Verified',
              type: 'text',
            },
            { field: 'role_name', header: 'Roles', type: 'text' },
            { field: 'last_login_at', header: 'Last login', type: 'text' },
            { field: 'pools', header: 'Pools', type: 'text' },
            { field: 'upcoming_meetings', header: 'Upcoming meetings', type: 'text' },
          ]"
        >
        </app-table>
    </app-layout>
</template>

<script>
import {defineComponent,reactive, onMounted} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import AppTable from "./Partials/UsersTable.vue";

export default defineComponent({
    props: {
        users: Object,
        roles:Array,
        errors: Object,
        searchFilters: Array,
        emailStatus: Array,
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
    setup(props) {
        const roles = reactive([]);
        const emailStatus = reactive([]);

        onMounted(() => {
            props.roles.forEach((item) => roles.push({
                "name": item.name,
                "value": item.name,
            }));
            props.emailStatus.forEach((item) => emailStatus.push({
                "name": item.name,
                "value": item.value,
            }));
        })

        return {
            roles
        }
    }
});
</script>

