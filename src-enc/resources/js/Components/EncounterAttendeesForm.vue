<template>
  <span>
    <span @click="startUpdating">
      <slot/>
    </span>
    <jet-dialog-modal :show="updating" modal-id="user" @close="closeModal">
      <template #title>Attendees of encounter: {{ encounter.uuid }} </template>
      <template #content>
        <div class="grid grid-cols-1 gap-2 p-6">
            <jet-label for="name" value="List of attendees" class="text-green-600"/>
                <div v-for="attendee in encounter.attendees">
                        {{ attendee.name }} ({{attendee.email}}) <span v-if="attendee.is_original"> - Requestor</span>
                </div>
        </div>
      </template>
      <template #footer>
        <jet-button class="ml-4 justify-center" @click="closeModal"
        >Close</jet-button>
      </template>
    </jet-dialog-modal>
  </span>
</template>

<script>
import {defineComponent, onMounted, ref, reactive, computed} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetLabel from "@/Components/Label.vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default defineComponent({
    props: {
        encounter: Object
    },

    components: {
        JetButton,
        JetDialogModal,
        JetLabel,
    },

    setup(props) {
        const updating = ref(false);

        const startUpdating = () => {
            updating.value = true;
        }

        const closeModal = () => {
            usePage().props.value.errors = {};
            updating.value = false;
        }

        return {
            startUpdating,
            closeModal,
            updating,
        }
    },
});
</script>
