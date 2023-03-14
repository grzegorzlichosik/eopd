<template>
    <TransitionRoot appear as="template" :show="show" :closeable="closeable" @close="close">
        <Dialog
            :initialFocus="checkoutButtonRef"
            class="overflow-hidden fixed inset-0 z-10"
            :open="isOpen">
            <TransitionChild
                enter="transition-opacity ease-in-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="transition-opacity ease-in-out duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0"
                as="template">
                <DialogOverlay class="absolute inset-0 bg-black bg-opacity-40"/>
            </TransitionChild>

            <TransitionChild
                enter="transform ease-in-out transition-transform duration-300"
                enter-from="translate-x-full"
                enter-to="translate-x-0"
                leave="transform ease-in-out transition-transform duration-300"
                leave-from="translate-x-0"
                leave-to="translate-x-full"
                as="template">
                <div class="flex flex-col fixed inset-y-0 right-0 w-full bg-white max-w-2xl px-10">
                    <div class="flex justify-between items-center pt-4 pb-10">
                        <DialogTitle class="text-xl">
                            <slot name="title"/>
                        </DialogTitle>
                        <slot name="button"/>
                    </div>
                    <div class="grow" style="overflow-y:auto">
                        <slot name="body"/>
                    </div>
                    <div class="flex flex-row justify-start pt-4 pb-6">
                        <slot name="footer"/>
                    </div>

                </div>
            </TransitionChild>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {defineComponent} from 'vue'
import {Dialog, DialogDescription, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';


export default defineComponent({
    emits: ['close'],

    components: {
        Dialog, DialogOverlay, DialogTitle, DialogDescription,
        TransitionRoot,
        TransitionChild,
    },

    props: {
        show: {
            default: false
        },
        closeable: {
            default: true
        },
    },

    methods: {
        close() {
            this.$emit('close')
        },
    }
})
</script>
<style>
.vcpg {
    border: none !important;
    border-radius: 0 !important;
}

.vcpg header {
    background-color: #eee !important;
    border: 1px solid #eee !important;
}

.vcp{
    margin-bottom: 10px;
}

.vcpg .vcp__body {
    border: 1px solid #eee !important;
}

</style>
