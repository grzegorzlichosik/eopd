<template>
  <span>
    <span @click="startDeleting">
      <slot/>
    </span>

    <jet-dialog-modal :show="deleting" @close="closeModal">
      <template #title>
        Delete Agent: {{ agent.email }}
      </template>

      <template #content>
          Are you sure you want to delete agent <strong>{{ agent.name }}</strong>?
           <jet-validation-errors class="mt-4"/>
      </template>

      <template #footer>
        <jet-secondary-button @click="closeModal">
          Cancel
        </jet-secondary-button>

        <jet-danger-button dusk="confirm-button"
                           class="ml-3"
                           @click="confirm"
                           :class="{ 'opacity-25': form.processing }"
                           :disabled="form.processing"
        >
          Confirm
        </jet-danger-button>
      </template>
    </jet-dialog-modal>
  </span>
</template>

<script>
import {defineComponent, ref} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDangerButton from "@/Components/DangerButton.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetValidationErrors from "@/Components/ValidationErrors.vue";
import {useForm} from "@inertiajs/inertia-vue3";

export default defineComponent({
    props: {
        agent: Array,
    },

    components: {
        JetButton,
        JetDangerButton,
        JetDialogModal,
        JetSecondaryButton,
        JetValidationErrors,
    },

    setup(props) {
        const deleting = ref(false);
        const form = useForm({})

        const startDeleting = () => {
            deleting.value = true;
        }

        const closeModal = () => {
            form.clearErrors()
            deleting.value = false;
        }

        const confirm = () => {
            form.delete(route('admin.flows.agent.delete', {
                flowUuid:  props.agent.flowUuid,
                uuid: props.agent.uuid
            }), {
                onSuccess: (res) => {
                    form.reset();
                    closeModal();
                },
            });
        }

        return {
            confirm,
            deleting,
            startDeleting,
            closeModal,
            form,
        }
    },
});
</script>
<style>
</style>
