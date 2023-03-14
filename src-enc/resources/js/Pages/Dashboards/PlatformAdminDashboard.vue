<template>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                <jet-card-button class="bg-brand-500" id="users" :href="route('admin.users')">
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-users" style="font-size: 30px;color: white"></i>
                        </div>
                    </template>
                    Manage users
                </jet-card-button>
                <jet-card-button class="bg-brand-800" id="organisations" :href="route('platform.organisations.index')">
                    <template #title>
                        <div style="text-align: center;margin-bottom: 15px">
                            <i class="fa fa-bank" style="font-size: 30px;"></i>
                        </div>
                    </template>
                    Manage Organisations
                </jet-card-button>
            </div>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import JetCardButton from '@/Components/CardButton'
import JetSecondaryButton from '@/Components/SecondaryButton'

export default defineComponent({

    components: {
        JetCardButton,
        JetSecondaryButton,
    },

})
</script>

<style scoped>

</style>
