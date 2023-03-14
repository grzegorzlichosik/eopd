<template>
    <BookingLayout title="Cancel Booking">
        <div class="py-12 w-3/4 max-w-5xl">
            <div class="w-full mx-auto sm:px-6 lg:px-8 ">
                <div class="p-2 bg-white border-border" style="min-height: auto">
                    <div class="max-w-5xl mx-auto py-2">
                        <h1 class="pb-3 text-3xl text-center text-bold">{{ flow.name }}</h1>
                    </div>
                    <h3 class="pb-3 mb-5 text-center">{{ flow.objective }}</h3>

                    <h3 class="pb-3 mb-5 text-center text-red-700 font-bold" v-if="error">{{ error }}</h3>
                    <div v-if="message">

                        <div class="w-2/3 mx-auto text-center">
                            <h1 class="text-2xl text-center text-bold mt-5 pt-5">
                                <h1 class="pb-3 mb-5 text-center text-gray-700 font-bold">{{ message }}</h1>
                                <form>
                                    <nav-link :href="route('encounters.booking.get', {uuid: flow.uuid})"
                                              method="get"
                                              as="button"
                                    >
                                        <jet-button
                                            class="ml-4 justify-center"
                                        >
                                            Make another booking
                                        </jet-button>
                                    </nav-link>

                                </form>
                            </h1>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </BookingLayout>
</template>

<script>

import {defineComponent} from 'vue'
import BookingLayout from '@/Layouts/BookingLayout.vue'
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import BookingCancel from './Partials/BookingCancel.vue';
import JetButton from "@/Components/Button.vue";
import NavLink from "@/Components/NavLink.vue";

export default defineComponent({
    components: {
        BookingLayout,
        Head,
        Link,
        BookingCancel,
        JetButton,
        NavLink,
    },
    props: {
        flow: Object,
        message: String,
        error: String,
    },

})
</script>
