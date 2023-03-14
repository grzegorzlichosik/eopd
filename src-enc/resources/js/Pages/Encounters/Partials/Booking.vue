<template>
    <div class="p-2 bg-white border-border" style="min-height: 33em">
        <div class="max-w-5xl mx-auto py-2">
            <h1 class="pb-3 text-3xl text-center text-bold">{{ flow.name }}</h1>
        </div>
        <h3 class="pb-3 mb-5 text-center">{{ flow.objective }}</h3>
        <div class="grid grid-cols-2 gap-4" v-if="!isBookingFormVisible">
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
                <SelectButton
                    v-if="channels.length > 1"
                    v-model="channel"
                    optionLabel="name"
                    optionValue="value"
                    :options="channels"
                    aria-labelledby="single"
                    class="text-center"
                />
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
                    class="overflow-y-auto px-2"
                    :class="{'h-96 mt-5' : channels.length === 1, 'h-80 mt-8' : channels.length !== 1}"
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
                    class="text-center relative"
                    :class="{'h-96 mt-5' : channels.length === 1, 'h-80 mt-8' : channels.length !== 1}"
                >
                    Please select date and type of the meeting<br> to get availability.
                </div>

                <div
                    v-if="(channel && selectedDate) && !loading && !times.length"
                    class="text-center relative"
                    :class="{'h-96 mt-5' : channels.length === 1, 'h-80 mt-8' : channels.length !== 1}"
                >
                    There is no availability for<br> selected date and type of the meeting.
                    <br>
                    Please select different date<br>or/and type of the meeting.
                </div>
            </div>
        </div>
        <div v-else>
            <div class="w-2/3 mx-auto" v-if="!isSuccessBookingVisible">
                <form @submit.prevent="submit">
                    <div v-if="flow.eventAttendees">
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3">Name
                            </div>
                            <div class="basis-1/3">Email
                            </div>
                            <div class="basis-1/3">Phone
                            </div>
                            <div class="basis-1/3">Is accepted
                            </div>

                        </div>
                        <div class="flex flex-row mb-3">
                            <div class="basis-1/3">{{ flow.eventAttendees.name }}
                                <span v-if="flow.eventAttendees.is_original">(Requestor)</span>
                            </div>
                            <div class="basis-1/3">{{ flow.eventAttendees.email }}
                            </div>
                            <div class="basis-1/3" v-if="flow.eventAttendees.phone_number">
                                {{ flow.eventAttendees.phone_number }}
                            </div>
                            <div class="basis-1/3" v-if="flow.eventAttendees.is_accepted">
                                <i class="fa fa-check" style="color: green"></i>
                            </div>
                            <div class="basis-1/3" v-else>
                                <i class="fa fa-close" style="color:red"></i>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="mb-4">
                            <JetLabel for="name" value="Name" :mandatory="true" class="text-base"/>
                            <JetInput
                                v-model="bookingForm.attendee_name"
                                type="text"
                                class="mt-1 block w-full"
                                autofocus
                            />
                            <JetInputError :message="bookingForm.errors.attendee_name" class="mt-2"/>
                        </div>
                        <div>
                            <JetLabel for="email" value="Email" :mandatory="true" class="text-base"/>
                            <JetInput
                                v-model="bookingForm.attendee_email"
                                type="email"
                                class="mt-1 block w-full"
                            />
                            <JetInputError :message="bookingForm.errors.attendee_email" class="mt-2"/>
                        </div>
                        <div class="mt-4" v-if="phoneNumberRequired">
                            <JetLabel for="phone_number" value="Phone Number" mandatory="true"/>
                            <vue-tel-input v-model="bookingForm.phone_number" name="phone_number" id="phone_number"
                                           required
                                           v-bind="bindProps" v-on:country-changed="countryChanged" style="border-radius: 0px;height:45px;--tw-border-opacity: 1;
    border-color: rgb(209 213 219 / var(--tw-border-opacity));"
                                           :preferredCountries="preferredCountries"
                            ></vue-tel-input>
                            <JetInputError class="mt-2" :message="bookingForm.errors.phone_number"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 my-5">
                        <div class="">
                            <jet-secondary-button
                                id="cancel_button"
                                @click="hideBookingForm"
                                :class="{ 'opacity-25': bookingForm.processing }"
                                :disabled="bookingForm.processing"
                            >
                                Cancel
                            </jet-secondary-button>
                        </div>
                        <div class="text-right">
                            <jet-button
                                @click="makeBooking"
                                class="ml-4 justify-end"
                                :class="{ 'opacity-25': bookingForm.processing }"
                                :disabled="bookingForm.processing"
                            >
                                Make Booking
                            </jet-button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="w-2/3 mx-auto text-center" v-else>
                <h1 class="text-2xl text-center text-bold mt-5 pt-5">
                    Congratulations! <br>
                    Your booking has been made successfully.

                    <jet-button
                        @click="resetWidget"
                        class="ml-4 justify-center"
                    >
                        Make another booking
                    </jet-button>
                </h1>
            </div>
        </div>
    </div>
</template>

<script>

import {defineComponent, onMounted, reactive, ref, watch} from 'vue'
import {useForm} from "@inertiajs/inertia-vue3";
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
        useForm
    },
    props: {
        flow: Object,
        preferredCountries: Array,
    },
    setup(props, context) {
        const channels = reactive([]);
        const channel = ref(null);
        const selectedDate = ref(moment().startOf('day').toDate());
        const minDate = ref(moment().startOf('day').toDate());
        const maxDate = ref(moment().add(14, 'days').endOf('day').toDate());
        const times = ref([]);
        const loading = ref(false);
        const isBookingFormVisible = ref(false);
        const isSuccessBookingVisible = ref(false);
        const phoneNumberRequired = ref(false);
        const countryCode = ref(null);
        const dialCode = ref(null);
        const bookingForm = useForm({
            date_time: null,
            attendee_name: null,
            attendee_email: null,
            extraAttendees: null,
            phone_number: null,
            country_code: '',
            dial_code: "",
            channel: null,
            event: null,
        })


        onMounted(() => {
            props.flow.channels.forEach(function (item) {
                channels.push({
                    "name": item?.type_name,
                    "value": item?.uuid,
                })

                if (item.is_default) {
                    channel.value = item?.uuid
                }
            });

            if (channels.length === 1) {
                channel.value = channels[0].value
            }

        })
        const countryChanged = (country) => {
            bookingForm.country_code = country.iso2;
            bookingForm.dial_code = country.dialCode
        }

        const showBookingForm = (availabilityItem) => {
            bookingForm.date_time = availabilityItem;
            bookingForm.channel = channel.value
            if (props.flow.event_uuid) {
                bookingForm.event = props.flow.event_uuid
                bookingForm.attendee_name = props.flow.eventAttendees.name
                bookingForm.attendee_email = props.flow.eventAttendees.email
            }
            isBookingFormVisible.value = true;
        }

        const makeBooking = () => {
            bookingForm.post(route('encounters.booking.post.store', {uuid: props.flow.uuid}), {
                    onSuccess: (res) => {
                    bookingForm.clearErrors();
                    bookingForm.reset();
                    isSuccessBookingVisible.value = true;
                },
            });
        }

        const hideBookingForm = () => {
            bookingForm.clearErrors();
            // bookingForm.reset();
            isBookingFormVisible.value = false;
        }

        const getAvailability = () => {
            times.value = [];
            if (selectedDate.value && channel.value) {
                loading.value = true;
                let payload = {
                    date: selectedDate.value,
                    channel_uuid: channel.value
                }

                return axios.post(route('encounters.availability.index', {uuid: props.flow.uuid}), payload).then((response) => {
                    times.value = response.data;
                    loading.value = false;
                });
            }
        }

        const resetWidget = () => {
            isBookingFormVisible.value = false;
            isSuccessBookingVisible.value = false;
            selectedDate.value = moment().startOf('day').toDate();
        }

        watch(channel, (channel) => {
            let index = props.flow.channels.findIndex(object => {
                return object.uuid === channel;
            })

            phoneNumberRequired.value = props.flow.channels[index].is_phone_required
            getAvailability()
        });

        watch(selectedDate, (selectedDate) => {
            getAvailability()
        });

        return {
            bindProps: {
                mode: "international",
                placeholder: "Enter a phone number",
                maxLen: 25,
                validCharactersOnly: true,
                autoFormat: false,
            },
            isBookingFormVisible,
            isSuccessBookingVisible,
            loading,
            times,
            channels,
            channel,
            selectedDate,
            minDate,
            maxDate,
            bookingForm,
            getAvailability,
            showBookingForm,
            hideBookingForm,
            makeBooking,
            resetWidget,
            countryChanged,
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
