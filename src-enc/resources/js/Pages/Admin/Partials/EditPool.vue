<template>
  <span>
    <span @click="startUpdating">
      <slot/>
    </span>
      <form>
    <jet-dialog-modal :show="updating" modal-id="user" @close="closeModal">
      <template #title> Edit Pool </template>
      <template #content>
        <div class="grid grid-cols-1 gap-2 p-6">

            <div class="mt-4">
                <JetLabel for="name" value="Pool name" mandatory="true"/>
                <jet-input
                    id="name"
                    v-model="form.name"
                    autocomplete="name"
                    autofocus
                    name="name"
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
            dusk="edit_pool"
            class="ml-4 justify-center"
            @click="update"
        >
          Update
        </jet-button>
      </template>
    </jet-dialog-modal>
          </form>
  </span>
</template>

<script>
import {defineComponent, ref, reactive} from "vue";
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
    props: {
        pool: Object
    },
    components: {
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        JetLabel,
        JetInput,
        JetValidationErrors,
        JetDropDownWidget,
        JetTextarea,
        MultiSelect,
        JetInputError,
        AutoComplete
    },

    setup(props) {
        const updating = ref(false);
        const selectedRoles = ref(null);
        const userOptions = reactive([]);

        const form = useForm({
            name: null,
        })

        const startUpdating = () => {
            updating.value = true;
            form.name = props.pool.name;
        }

        const update = () => {
            form.put(route("admin.pools.show", {uuid: props.pool.uuid}), {
                onSuccess: (res) => {
                    closeModal();
                },
            });
        }

        const closeModal = () => {
            form.reset();
            form.clearErrors();
            updating.value = false;
        }

        return {
            startUpdating,
            update,
            closeModal,
            updating,
            selectedRoles,
            form,
            userOptions
        }
    },
});
</script>
