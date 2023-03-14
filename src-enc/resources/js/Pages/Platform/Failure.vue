<template>
    <AppLayout title="Link Calendar">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Link Calendar
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 mb-10">
                <h2 class="pb-3 text-3xl text-center" style="text-align: center"><b>Failed</b></h2>
                <span>{{ failed }}</span>
                <div style="text-align: center">
                    <jet-secondary-button class="ml-4 justify-center" @click="cancel"
                    >Cancel
                    </jet-secondary-button>
                    <jet-button
                        dusk="retry_master"
                        class="ml-4 justify-center"
                        @click="retry"
                    >
                        Retry
                    </jet-button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>

import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import JetButton from "@/Components/Button.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";

export default defineComponent({
    props: {
        failed: String
    },
    components: {
        AppLayout,
        JetButton,
        JetSecondaryButton

    },
    methods: {
        retry() {
            this.$inertia.get(route('platform.organisations.retry'));
        },

        cancel(){
            this.$inertia.get(route('platform.organisations.index'));
        }
    }
})
</script>
