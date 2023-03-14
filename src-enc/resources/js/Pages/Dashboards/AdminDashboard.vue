<template>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div
                class="grid grid-cols-1 gap-2  md:grid-cols-3"
                style="justify-content: center;display: flex"
            >
                <jet-card-button
                    class="bg-gray-600"
                    id="encounter"
                    :href="route('admin.encounters.upcoming')"
                >
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-clock-o" aria-hidden="true" style="font-size: 30px;"></i>
                        </div>
                    </template>
                    Encounters
                </jet-card-button>
                <jet-card-button
                    class="bg-yellow-700"
                    id="resources"
                    :href="route('admin.resources.locations.index')"
                >
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-globe" aria-hidden="true" style="font-size: 30px;"></i>
                        </div>
                    </template>
                    Manage resources
                </jet-card-button>
                <jet-card-button
                    class="bg-teal-400"
                    id="flows"
                    :href="route('admin.flows.index')"
                >
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-tasks" aria-hidden="true" style="font-size: 30px;"></i>
                        </div>
                    </template>
                    Manage Flows
                </jet-card-button>
            </div>

            <div
                class="grid grid-cols-1 gap-2  md:grid-cols-2"
                style="justify-content: center;display: flex"
            >
                <jet-card-button
                    class="bg-brand-500"
                    id="users"
                    :href="route('admin.users')">
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-users" aria-hidden="true" style="font-size: 30px;color: white"></i>
                        </div>
                    </template>
                    Manage users
                </jet-card-button>
                <jet-card-button
                    class="bg-gray-400"
                    id="pools"
                    :href="route('admin.pools')"
                >
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-object-group" aria-hidden="true" style="font-size: 30px;"></i>
                        </div>
                    </template>
                    Manage pools
                </jet-card-button>
            </div>

            <div
                :class="[ ($page.props.user.nylas_account_id || $page.props.user.organisation.nylas_account_id) ? 'grid grid-cols-1 md:grid-cols-1' : 'grid grid-cols-1 gap-2  md:grid-cols-2']"
                style="justify-content: center;display: flex"
            >
                <jet-card-button class="bg-brand-200"
                                 href="/calendar/link/native/init?link=myCalendar"
                                 id="form_trigger"
                                 dusk="link_my_calendar"
                                 v-if="!$page.props.user.nylas_account_id"
                >
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-calendar" aria-hidden="true" style="font-size: 30px;"></i>
                        </div>
                    </template>
                    Link My Calendar
                </jet-card-button>
                <jet-card-button class="bg-brand-200"
                                 :href="route('agent.encounters.all')"
                                 dusk="view_my_schedule"
                                 v-else-if="$page.props.user.nylas_account_id && $page.props.user.is_agent"
                >
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-calendar" aria-hidden="true" style="font-size: 30px;"></i>
                        </div>
                    </template>
                   View My Schedule
                </jet-card-button>
                <jet-card-button v-if="!$page.props.user.organisation.nylas_account_id"
                                 class="bg-blue-300"
                                 href="/resources/link/native/init"
                                 dusk="link_room_calendar"
                >
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-calendar-o" aria-hidden="true" style="font-size: 30px;"></i>
                        </div>
                    </template>
                    Link Room Calendar
                </jet-card-button>
            </div>
        </div>
        <div v-if="$page.props.user.nylas_account_id">
            <link-calendar></link-calendar>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import JetCardButton from '@/Components/CardButton'
import JetSecondaryButton from '@/Components/SecondaryButton'
import LinkCalendar from "../LinkCalendar/Calendar";

export default defineComponent({

    components: {
        JetCardButton,
        JetSecondaryButton,
        LinkCalendar
    }
})

</script>
