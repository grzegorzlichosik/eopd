<template>
    <AppLayout title="Link Calendar">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Link GSuite to Nylas
            </h2>
        </template>

        <jet-dialog-modal :show="true" modal-id="user">
            <template #title> Congratulations</template>
            <template #content>
                <div class="grid grid-cols-1 gap-2 p-6">
                    <div>
                        <h1>{{ message }}</h1>

                    </div>
                </div>
            </template>
            <template #footer>
                <jet-button
                    dusk="return"
                    class="ml-4 justify-center"
                    @click="returnOrganisations"
                >
                    Return to organisations
                </jet-button>
            </template>
        </jet-dialog-modal>
    </AppLayout>
</template>

<script>

import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import JetDialogModal from "@/Components/DialogModal.vue";
import JetButton from "@/Components/Button.vue";

export default defineComponent({

    props:{
      message: String
    },
    components: {
        AppLayout,
        JetDialogModal,
        JetButton

    },
    methods: {
        returnOrganisations() {
            this.$inertia.get(route('platform.organisations.index'))

        }
    }

})
</script>
