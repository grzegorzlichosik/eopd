<template>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div
                :class="[ ( $page.props.user.nylas_account_id || $page.props.user.organisation.nylas_account_id ) ? 'grid grid-cols-1 md:grid-cols-1' : 'grid grid-cols-1 gap-2  md:grid-cols-2' ]"
                style="justify-content: center;display: flex">
                <jet-card-button
                    class="bg-brand-200"
                    id="calendar"
                    href="/calendar/link/native/init"
                    v-if="!$page.props.user.nylas_account_id"
                    dusk="link_my_calendar"
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
                                 id="view_my_schedule"
                                 v-else
                >
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-calendar" aria-hidden="true" style="font-size: 30px;"></i>
                        </div>
                    </template>
                    View My Schedule
                </jet-card-button>
                <jet-card-button
                    class="bg-blue-300"
                    href="/resources/link/native/init"
                    id="form_trigger"
                    dusk="link_room_calendar"
                    v-if="!$page.props.user.organisation.nylas_account_id"
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
        LinkCalendar,
        JetCardButton,
        JetSecondaryButton,
    },

})
</script>


<style scoped>

</style>
