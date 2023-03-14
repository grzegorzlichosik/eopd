<template>
    <BookingLayout title="Add Participants to Booking">
        <div class="py-12 w-3/4 max-w-5xl">
            <div class="w-full mx-auto sm:px-6 lg:px-8 ">
                <form>
                    <div class="p-4 bg-white border-border" style="min-height: auto">
                        <div class="max-w-5xl mx-auto py-2">
                            <h1 class="pb-3 text-3xl text-center text-bold">{{ event.flow.name }}</h1>
                        </div>
                        <h3 class="pb-3 mb-5 text-center">{{ event.flow.objective }}</h3>
                        <div v-if="maxParticipants <= 0  && !isSuccessBookingVisible">
                            <h2 class="pb-3 text-lg text-center text-bold">
                                Maximum attendees that can be added for this meeting is reached.
                            </h2>
                        </div>
                        <div v-else>
                            <div v-if="!isSuccessBookingVisible">
                                <h2 class="pb-3 text-lg text-center text-bold">
                                    Add participants to the booking scheduled on
                                    {{ toLocalDate(event.scheduled_at, 'DD/MM/YY') }}
                                </h2>
                                <table class="w-full text-left">
                                    <tr>
                                        <th class="w-5/12">Name</th>
                                        <th class="w-6/12">Email</th>
                                        <th class="w-1/12">&nbsp;</th>
                                    </tr>
                                    <tr v-for="(attendee, index) in form.participants" :key="index">
                                        <td class="pr-2 align-top">
                                            <JetInput
                                                v-model="form.participants[index].name"
                                                type="text"
                                                :key="index"
                                                class="mt-1 block w-full"
                                                name="participants[][name]"
                                            />
                                            <div class="text-sm text-red-500"
                                                 v-if="form.errors['participants.' + index + '.name']">
                                                {{ form.errors['participants.' + index + '.name'] }}
                                            </div>
                                        </td>
                                        <td class="align-top">
                                            <JetInput
                                                v-model="form.participants[index].email"
                                                type="email"
                                                :key="index"
                                                class="mt-1 block w-full"
                                                name="participants[][email]"
                                            />
                                            <div class="text-sm text-red-500"
                                                 v-if="form.errors['participants.' + index + '.email']">
                                                {{ form.errors['participants.' + index + '.email'] }}
                                            </div>
                                        </td>
                                        <td class="text-right align-top">
                                            <jet-button
                                                v-if="form.participants.length > 1"
                                                type="button"
                                                @click="removeAttendee"
                                                class="justify-start bg-red-500"
                                                :class="{ 'opacity-25': form.processing }"
                                            >
                                                X
                                            </jet-button>
                                        </td>
                                    </tr>
                                </table>

                                <div class="grid grid-cols-2 gap-4 my-5">
                                    <div>
                                        <jet-button
                                            v-if="maxParticipants > form.participants.length"
                                            type="button"
                                            @click="addAttendee"
                                            class="justify-start bg-green-500"
                                            :class="{ 'opacity-25': form.processing }"
                                        >
                                            Add Attendee
                                        </jet-button>
                                    </div>
                                    <div class="text-right">
                                        <jet-button
                                            type="button"
                                            @click="addParticipants"
                                            class="ml-4 justify-end"
                                            :class="{ 'opacity-25': form.processing }"
                                        >
                                            Submit
                                        </jet-button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center mb-5">
                                <h2 class="pb-3 text-lg text-center text-bold">Congratulations!</h2>
                                Attendees have been added successfully.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </BookingLayout>
</template>

<script>

import {defineComponent, reactive, onMounted, ref} from 'vue'
import BookingLayout from '@/Layouts/BookingLayout.vue'
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import BookingCancel from './Partials/BookingCancel.vue';
import JetInput from "@/Components/Input.vue";
import JetInputError from "@/Components/InputError.vue";
import JetButton from "@/Components/Button.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";

export default defineComponent({
    components: {
        BookingLayout,
        Head,
        Link,
        BookingCancel,
        JetInput,
        JetInputError,
        JetButton,
        JetSecondaryButton,
    },
    props: {
        event: Object,
        maxParticipants: Number,
        message: String
    },

    setup(props) {
        const isSuccessBookingVisible = ref(false);

        const attendee = {
            name: null,
            email: null,
        }

        const form = useForm({
            participants: reactive([])
        })

        const addAttendee = () => {
            form.participants.push(Object.assign({}, attendee));
        }

        const removeAttendee = (index) => {
            form.participants.splice(index, 1);
        }

        const addParticipants = () => {
            form.put(route('encounters.booking.store_participants', {uuid: props.event.uuid}), {
                onSuccess: (res) => {
                    form.clearErrors();
                    form.reset();
                    isSuccessBookingVisible.value = true;
                },
            });
        }

        onMounted(() => {
            form.participants.push(Object.assign({}, attendee));
        })

        return {
            isSuccessBookingVisible,
            form,
            addAttendee,
            removeAttendee,
            addParticipants,
        }
    }
})
</script>
