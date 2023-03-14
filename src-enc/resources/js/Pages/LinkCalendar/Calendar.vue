<template>
    <div class="py-12">
        <div style="text-align: end">
            Calendar linked with email:
            <span class="font-semibold">
                    {{ $page.props.user.office_365_email_id }}
                </span>
        </div>
        <br>
        <div style="position: absolute;
    display: flex;
    flex: 1;
    justify-content: center;
    align-items: center;
    width: 980px !important;
    height: 80vh;
    z-index: 2;" v-if="Loading">
            <ProgressSpinner/>
        </div>
        <event-modal :single="eventData">
        </event-modal>
        <div>
            <FullCalendar :events="events" :options="options"/>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import '@fullcalendar/core/vdom'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid';
import ProgressSpinner from 'primevue/progressspinner';
import EventModal from './EventModal';

export default defineComponent({

    components: {
        FullCalendar,
        ProgressSpinner,
        EventModal
    },
    data() {
        return {
            Loading: false,
            eventData: [],
            options: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                timeZone: 'UTC',
                initialView: 'timeGridWeek',
                slotMinTime: '07:00:00',
                slotMaxTime: '19:00:00',
                allDaySlot: true,
                nowIndicator: true,
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                loading: this.handleLoading,
                showEventModal: false,

                eventSources: [
                    {
                        events(date, callback) {
                            axios.get(route('calendar.index', {
                                start: date.startStr,
                                end: date.endStr

                            })).then(response => {
                                callback(response.data.data)
                            })
                        }
                    }
                ],
                eventClick: this.handleClick,
            }
        }
    },

    methods: {

        handleLoading(isLoading) {
            this.Loading = isLoading
        },
        handleClick(info) {
            let Events = {
                show: !this.showEventModal,
                data: info.event
            }
            this.eventData = Events;
        }
    }
})
</script>

<style scoped>

@media screen and (max-width: 960px) {
    ::v-deep(.fc-header-toolbar) {
        display: flex;
        flex-wrap: wrap;
    }

}

@media screen and (min-width: 320px) and (max-width: 780px) {
    ::v-deep(.fc-header-toolbar) {
        display: flex;
        flex-wrap: wrap;
    }

    .p-progress-spinner {
        width: 50% !important;
        height: 50%;
    }
}
</style>
