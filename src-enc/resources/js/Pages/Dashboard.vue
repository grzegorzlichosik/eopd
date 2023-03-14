<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 mb-10">
                <h1 class="pb-3 text-3xl text-center"><b>Welcome</b></h1>
                <platform-admin-dashboard v-if="$page.props.user?.organisation.is_platform"/>
                <super-admin-dashboard v-else-if="$page.props.user.is_super_admin"/>
                <admin-dashboard v-else-if="$page.props.user.is_admin"/>
                <agent-dashboard v-else-if="$page.props.user.is_agent"/>
            </div>
        </div>
    </AppLayout>
</template>

<script>

import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SuperAdminDashboard from "./Dashboards/SuperAdminDashboard";
import AdminDashboard from "./Dashboards/AdminDashboard";
import AgentDashboard from "./Dashboards/AgentDashboard";
import PlatformAdminDashboard from "./Dashboards/PlatformAdminDashboard";

export default defineComponent({
    components: {
        AdminDashboard,
        PlatformAdminDashboard,
        SuperAdminDashboard,
        AgentDashboard,
        AppLayout,

    },
})
</script>
