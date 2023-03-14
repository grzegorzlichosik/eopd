<template>
  <span>

    <jet-dialog-modal :show="single.show" @close="closeModal">
      <template #title> Event Details</template>

        <template #content>
        <div class="grid grid-cols-1 gap-2 p-6">
            <h1>Title : {{ single.data.title }}</h1>
            <h1>Start : {{ single.data.start }}</h1>
            <h1>End : {{ single.data.end }}</h1>
        </div>
        </template>

      <template #footer>
        <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
        >Cancel</jet-secondary-button>
      </template>
    </jet-dialog-modal>
  </span>
</template>

<script>
import {defineComponent, onMounted, ref, reactive} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import {useForm, usePage} from "@inertiajs/inertia-vue3";

export default defineComponent({

    props: {
        single: {
            data: Object,
            show: Boolean,
        }
    },

    components: {
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
    },

    setup(props) {
        const closeModal = () => {
            usePage().props.single = {};
            props.single.show = false;
        }

        return {
            closeModal,
        }
    },
});
</script>
