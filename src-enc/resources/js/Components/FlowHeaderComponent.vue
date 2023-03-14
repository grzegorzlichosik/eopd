<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <div style="text-align: left">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="flow-header">
                Flow Name: {{ flow.name }}
            </h2>
        </div>
        <div style="text-align: right">
            <jet-button-small
                type="button"
                class="ml-4 justify-end bg-orange-400"
                id="copy_uuid"
                @click="copyUuid(flow.uuid)"
            >COPY UUID

            </jet-button-small>
            <jet-button-small
                type="button"
                class="ml-4 justify-end bg-gray-700"
                id="copy_booking_link"
                @click="copyBookingLink(flow.uuid)"
            >COPY BOOKING LINK

            </jet-button-small>
            <update-flow-form :flow="flow" :channelTypes="channelTypes">
                <jet-button-small
                    type="button"
                    class="ml-4 justify-end bg-brand-300"
                    id="edit_new_flow"
                    dusk="edit_new_flow"
                >EDIT
                </jet-button-small>
            </update-flow-form>
            <div>
                         <span v-if="copied" class="text-brand-600 text-sm">
                            <br>Copied uuid.
                        </span>
            </div>
            <div>
                         <span v-if="copiedURL" class="text-brand-600 text-sm">
                            <br>Copied booking link.
                        </span>
            </div>
        </div>

    </div>
</template>

<script>
import {defineComponent} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetLabel from "@/Components/Label.vue";
import JetButtonSmall from "@/Components/ButtonSmall.vue";
import UpdateFlowForm from "./../Pages/Admin/Partials/EditFlowForm.vue";
import {useToast} from 'vue-toast-notification';

export default defineComponent({
    props: {
        flow: Object,
        channelTypes: Object,
    },

    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
        JetLabel,
        UpdateFlowForm,
        JetButtonSmall,
    },

    setup(props) {

        const toast = useToast({position: "bottom-right"});

        const copyUuid = async (uuid) => {
            try {
                await navigator.clipboard.writeText(uuid);
                toast.info('Uuid has been copied.')
            } catch ($e) {
                toast.error('Cannot copy.')
            }
        }

        const copyBookingLink = async (uuid) => {
            try {
                await navigator.clipboard.writeText(window.location.origin + '/bookings/' + uuid);
                toast.info('Booking url has been copied.')
            } catch ($e) {
                toast.error('Cannot copy.')
            }
        }

        return {
            copyUuid,
            copyBookingLink
        }

    },
});
</script>
