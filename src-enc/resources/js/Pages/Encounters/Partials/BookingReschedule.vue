<template>
    <div v-if="!isCalendarVisible"
         class="p-2 bg-white border-border" style="min-height: 33em">
        <SuccessMessage :message="successMessage" v-if="successMessage"/>
        <BookingDetails :event="event"></BookingDetails>
    </div>
    <div v-else class="p-4 bg-white border-border" style="min-height: 33em">
        <ErrorMessage :message="errorMessage" v-if="errorMessage"/>
        <ErrorMessage :message="$page.props.backendError" v-if="$page.props.backendError"/>
        <div class="max-w-5xl mx-auto py-2">
            <h1 class="pb-3 text-3xl text-center text-bold">{{ event.flow.name }}</h1>
        </div>
        <h3 class="pb-3 mb-5 text-center">{{ event.flow.objective }}</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <Calendar
                    v-model="selectedDate"
                    dateFormat="yy-mm-dd"
                    :inline="true"
                    :minDate="minDate"
                    :maxDate="maxDate"
                >
                </Calendar>
            </div>
            <div>
                <div
                    v-if="loading"
                    class="text-center relative"
                    :class="{'h-96 mt-5' : channels.length === 1, 'h-80 mt-8' : channels.length !== 1}"
                >
                    <ProgressSpinner aria-label="Basic ProgressSpinner" class="mt-8"/>
                    <div class="mt-5">
                        Loading...
                    </div>
                </div>
                <div
                    v-if="!loading && times.length"
                    class="overflow-y-auto px-2  h-96 mt-5"
                >
                    <jet-button
                        v-for="item in times"
                        class="w-full justify-center bg-brand-100 border border-brand-500 text-black hover:bg-brand-900
                        hover:text-white hover:border-brand-900 mb-2 py-2.5"
                        @click="showBookingForm(item)"
                    >
                        {{ item.startTime }}
                    </jet-button>
                </div>
                <div
                    v-if="!channel || !selectedDate"
                    class="text-center relative h-96 mt-5"
                >
                    Please select date to get availability.
                </div>

                <div
                    v-if="(channel && selectedDate) && !loading && !times.length"
                    class="text-center relative h-96 mt-5"
                >
                    There is no availability for<br> selected date.
                    <br>
                    Please select different one.
                </div>
            </div>
        </div>
        <hr class="my-5">
        <div class="w-full mx-auto my-4">
            <div class="flex flex-row mb-3">
                <div class="basis-1/3 font-bold">Attendees List</div>
            </div>
            <div class="flex flex-row mb-3">
                <div class="basis-1/3">Name</div>
                <div class="basis-1/3">Email</div>
                <div class="basis-1/3">Accepted</div>
            </div>
            <div class="flex flex-row mb-3" v-for="attendee in event.attendees">
                <div class="basis-1/3">{{ attendee.name }}
                    <span v-if="attendee.is_original">(Requestor)</span>
                </div>
                <div class="basis-1/3">{{ attendee.email }}
                </div>
                <div class="basis-1/3">
                    <i class="fa fa-check text-green-500" v-if="attendee.is_accepted"></i>
                    <i class="fa fa-close text-red-500" v-else></i>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 my-5">
                <div class="">
                    <jet-secondary-button
                        id="cancel_button"
                        @click="resetWidget"
                    >
                        Cancel
                    </jet-secondary-button>
                </div>
                <div class="text-right">
                    <jet-button
                        @click="reschedule"
                        class="ml-4 justify-end"
                        :disbled="!isBookingFormVisible"
                        :class="{ 'opacity-25': !isBookingFormVisible }"
                    >
                        Update Booking
                    </jet-button>
                </div>
            </div>
        </div>
    </div>
    <div class="w-2/3 mx-auto" v-if="event && !isCalendarVisible">
        <br>
        <div class="text-center" v-if="event.tsm_current_state === 'Scheduled'">
            <jet-button @click="showCalendar()"
                        class="ml-4 justify-center"
            >
                Reschedule Booking
            </jet-button>
        </div>
        <div class="text-center" v-else>
            <nav-link :href="route('encounters.booking.get', {uuid: event.flow.uuid})"
                      method="get"
                      as="button"
            >
                <jet-button
                    class="ml-4 justify-center"
                >
                    Make another booking
                </jet-button>
            </nav-link>
        </div>
    </div>

</template>

<script>

import {defineComponent, onMounted, reactive, ref, watch} from 'vue'
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import DatePicker from "@/Components/DatePicker.vue";
import JetLabel from "@/Components/Label.vue";
import JetInput from "@/Components/Input.vue";
import JetInputError from "@/Components/InputError.vue";
import JetDropDownWidget from "@/Components/DropDownWidget";
import JetButton from "@/Components/Button.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import Calendar from 'primevue/calendar';
import SelectButton from 'primevue/selectbutton';
import ProgressSpinner from 'primevue/progressspinner';
import moment from 'moment';
import NavLink from "@/Components/NavLink.vue";
import SuccessMessage from "@/Components/SuccessMessage.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";
import BookingDetails from "./BookingDetails";

export default defineComponent({
    components: {
        DatePicker,
        JetLabel,
        JetInput,
        JetInputError,
        JetDropDownWidget,
        JetButton,
        JetSecondaryButton,
        JetButtonSmall,
        Calendar,
        SelectButton,
        ProgressSpinner,
        useForm,
        SuccessMessage,
        ErrorMessage,
        NavLink,
        BookingDetails,
    },
    props: {
        event: Object
    },
    setup(props) {
        const successMessage = ref(null);
        const errorMessage = ref(null);
        const isCalendarVisible = ref(null);

        const channels = reactive([]);
        const channel = ref(null);
        const selectedDate = ref(null);
        const minDate = ref(moment().startOf('day').toDate());
        const maxDate = ref(moment().add(14, 'days').endOf('day').toDate());
        const times = ref([]);
        const loading = ref(false);
        const isBookingFormVisible = ref(false);
        const isSuccessBookingVisible = ref(false);
        const phoneNumberRequired = ref(false);
        const countryCode = ref(null);
        const dialCode = ref(null);
        const payload = ref(null);

        const reschedule = () => {
            errorMessage.value = null;
            axios.put(route('encounters.booking.update', {uuid: props.event.uuid}), payload.value)
                .then((response) => {
                    successMessage.value = response.data.message;
                    isCalendarVisible.value = false;
                    usePage().props.value.event = response.data.event;
                })
                .catch((response) => {
                    errorMessage.value = response.response.data.message;
                });
        }

        const showCalendar = () => {
            isCalendarVisible.value = true;
        }

        onMounted(() => {
            channel.value = props.event.channel.uuid
            selectedDate.value = moment().startOf('day').toDate();
        })

        const getAvailability = () => {
            times.value = [];
            if (selectedDate.value && channel.value && isCalendarVisible.value) {
                loading.value = true;
                let payload = {
                    date: selectedDate.value,
                    channel_uuid: channel.value
                }

                return axios.post(route('encounters.availability.index', {uuid: props.event.flow.uuid}), payload).then((response) => {
                    times.value = response.data;
                    loading.value = false;
                });
            }
        }

        const showBookingForm = (availabilityItem) => {
            payload.value = {date_time: availabilityItem}
            isBookingFormVisible.value = true;
        }

        const resetWidget = () => {
            isBookingFormVisible.value = false;
            isCalendarVisible.value = false;
        }

        watch(selectedDate, (selectedDate) => {
            getAvailability()
        });

        return {
            reschedule,
            showCalendar,
            showBookingForm,
            resetWidget,
            isCalendarVisible,
            successMessage,
            errorMessage,
            isBookingFormVisible,
            isSuccessBookingVisible,
            loading,
            times,
            channels,
            channel,
            selectedDate,
            minDate,
            maxDate,
            phoneNumberRequired,
            countryCode,
            dialCode,
        }
    }

})
</script>
<style>
.p-inputtext,
.p-togglebutton,
.p-selectbutton,
.p-inputgroup {
    box-shadow: none;
}

.p-button:focus {
    box-shadow: none;
    outline: none;
}

</style>
