<template>
    <BookingLayout title="Reschedule Booking">
        <div class="py-12 w-3/4 max-w-5xl">
            <div class="w-full mx-auto sm:px-6 lg:px-8 ">
                <BookingReschedule :event="event"/>
            </div>
        </div>
    </BookingLayout>
</template>

<script>

import {defineComponent} from 'vue'
import BookingLayout from '@/Layouts/BookingLayout.vue'
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import BookingReschedule from './Partials/BookingReschedule.vue';

export default defineComponent({
    components: {
        BookingLayout,
        Head,
        Link,
        BookingReschedule,
    },
    props:{
        event: Object,
    }
})
</script>
