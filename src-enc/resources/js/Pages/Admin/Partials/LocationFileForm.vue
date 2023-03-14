<template>

    <span>
      <span @click="startCreating">
        <slot/>
      </span>
      <jet-dialog-modal :show="creating" modal-id="user" @close="closeModal">
        <template #title>Upload travel instructions</template>
          <template #content>
            <div class="grid grid-cols-1 gap-2 p-6">
                <form>

               <div class="mt-4">
                  <JetLabel for="travel_instructions" value="Travel instructionss"/>
                   <input type="file" v-on:change="onFileChange" accept="application/pdf"
                          class="inline-flex items-center px-4 py-2 bg-brand-300 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 active:bg-brand-900 focus:outline-none focus:border-brand-900 focus:ring focus:ring-brand-300 disabled:opacity-25 transition w-full"/>
                   <JetInputError :message="form.errors.file" class="mt-2"/>
              </div>
            </form>
          </div>

          </template>
          <template #footer>
            <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
            >Cancel</jet-secondary-button>
            <jet-button
                dusk="upload"
                class="ml-4 justify-center"
                id="upload_file"
                @click="update"
            >
               Save
            </jet-button>
          </template>
      </jet-dialog-modal>
    </span>
</template>

<script>
import {defineComponent, ref} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetLabel from "@/Components/Label.vue";
import JetInput from "@/Components/Input.vue";
import JetValidationErrors from "@/Components/ValidationErrors.vue";
import JetInputError from '@/Components/InputError.vue';
import {useForm} from "@inertiajs/inertia-vue3";


export default defineComponent({
    props: {
        uuid: String,

    },
    computed: {
        tzGuess() {
            return moment.tz.guess(true);
        }
    },

    components: {
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        JetLabel,
        JetInput,
        JetValidationErrors,
        JetInputError,
    },
    setup(props) {
        const creating = ref(false);
        let created = ref(false);
        const form = useForm({
            file: "",
        })

        const startCreating = () => {
            creating.value = true;
        }

        const onFileChange = (e) => {
            form.file = e.target.files[0];
        }

        const update = (e) => {
            form.post(route("admin.resources.locations.upload", {uuid: props.uuid}), {
                onSuccess: (res) => {
                    closeModal();
                },
            });
        }

        const closeModal = () => {
            form.reset();
            form.clearErrors();
            props.errors = {};
            creating.value = false;

        }

        return {
            startCreating,
            update,
            closeModal,
            creating,
            form,
            onFileChange,
            created,
        }
    },

});
</script>
