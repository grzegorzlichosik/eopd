<template>
    <div class="p-2 bg-white border-border" style="min-height: 33em">
        <SuccessMessage :message="successMessage" v-if="successMessage"/>
        <ErrorMessage :message="errorMessage" v-if="errorMessage"/>
        <ErrorMessage :message="$page.props.backendError" v-if="$page.props.backendError"/>
        <div class="p-2 bg-white border-border" style="min-height: 33em">
            <BookingDetails :event="event" v-if="event"/>
        </div>
    </div>
    <div class="w-2/3 mx-auto" v-if="event">
        <br>
        <div class="text-center" v-if="event.tsm_current_state === 'Scheduled'">
            <jet-button @click="cancel()"
                        class="ml-4 justify-center"
            >
                Cancel Booking
            </jet-button>
        </div>
        <div class="text-center" v-else>
            <nav-link :href="route('encounters.booking.get', {uuid: $page.props.event.flow.uuid})"
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

import {defineComponent, ref} from 'vue'
import {usePage} from "@inertiajs/inertia-vue3";
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
        NavLink,
        SuccessMessage,
        ErrorMessage,
        BookingDetails,
        usePage,
    },
    props: {
        event: {
            type: Object,
            default: null,
        },
    },
    setup(props) {
        const successMessage = ref(null);
        const errorMessage = ref(null);
        const cancel = () => {
            errorMessage.value = null;
            axios.delete(route('encounters.booking.destroy', {uuid: props.event.uuid}))
                .then((response) => {
                    successMessage.value = response.data.message;
                    usePage().props.value.event.tsm_current_state = 'Cancelled'
                })
                .catch((response) => {
                    errorMessage.value = response.response.data.message;
                });
        }

        return {
            cancel,
            successMessage,
            errorMessage,
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
