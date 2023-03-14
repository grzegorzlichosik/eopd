<template>
  <span>
    <span @click="startCreating">
      <slot/>
    </span>
      <form>
    <jet-dialog-modal :show="creating" modal-id="user" @close="closeModal">
      <template #title> Create Pool </template>
      <template #content>
        <div class="grid grid-cols-1 gap-2 p-6">

            <div class="mt-4">
                <JetLabel for="name" value="Pool name" mandatory="true"/>
                <jet-input
                    id="name"
                    v-model="form.name"
                    autocomplete="name"
                    autofocus
                    class="mt-1 block w-full"
                    type="text"
                />
                <JetInputError :message="form.errors.name" class="mt-2"/>
            </div>


        </div>
      </template>
      <template #footer>
        <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
        >Cancel</jet-secondary-button>
        <jet-button
            dusk="create_pool"
            class="ml-4 justify-center"
            @click="create"
        >
          Add New Pool
        </jet-button>
      </template>
    </jet-dialog-modal>
          </form>
  </span>
</template>

<script>
import {defineComponent, onMounted, ref, reactive} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetLabel from "@/Components/Label.vue";
import JetDropDownWidget from "@/Components/DropDownWidget";
import JetInput from "@/Components/Input.vue";
import JetValidationErrors from "@/Components/ValidationErrors.vue";
import JetTextarea from '@/Components/Textarea.vue';
import JetInputError from '@/Components/InputError.vue';
import MultiSelect from 'primevue/multiselect';
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import AutoComplete from 'primevue/autocomplete';

export default defineComponent({
    components: {
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        JetLabel,
        JetInput,
        JetInputError,
    },

    setup() {
        const creating = ref(false);
        const selectedRoles = ref(null);
        const userOptions = reactive([]);

        const form = useForm({
            name: null,
        })

        const startCreating = () => {
            creating.value = true;
        }

        const create = () => {
            form.post(route("admin.pools.create"), {
                onSuccess: (res) => {
                    form.reset();
                    closeModal();
                },
            });

        }

        const closeModal = () => {
            form.clearErrors();
            form.reset();
            creating.value = false;
        }

        return {
            startCreating,
            create,
            closeModal,
            creating,
            selectedRoles,
            form,
            userOptions
        }
    },
});
</script>
