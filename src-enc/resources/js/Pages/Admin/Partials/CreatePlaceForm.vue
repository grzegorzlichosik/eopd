<template>
  <span>
    <span @click="startCreating">
      <slot/>
    </span>
      <form>
    <jet-dialog-modal :show="creating" modal-id="place" @close="closeModal">
      <template #title> Create Place </template>
      <template #content>
        <div class="grid grid-cols-1 gap-2 p-6">

            <div class="mt-4">
                <JetLabel for="name" value="Name" mandatory="true"/>
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

             <div class="mt-4">
                <JetLabel for="description" value="Description" mandatory="true"/>
                <JetTextarea
                    id="description"
                    v-model="form.description"
                    type="text"
                    maxlength="250"
                    class="mt-3 pt-3 pb-3.5 pl-3 pr-3 border"
                    required
                    autocomplete="description"
                />
                <JetInputError :message="form.errors.description" class="mt-2"/>
            </div>

             <div class="mt-4">
                <JetLabel for="location" value="Location" mandatory="true"/>
                   <jet-drop-down-widget
                       class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm h-11"
                       v-model="form.location_uuid"
                       :items="locations"
                   />
                <JetInputError :message="form.errors.location_uuid" class="mt-2"/>
            </div>

             <div class="mt-4">
                <JetLabel for="type" value="Type" mandatory="true"/>
                 <jet-drop-down-widget
                     class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm h-11"
                     v-model="form.place_type_uuid"
                     :items="placeTypes"
                 />
                <JetInputError :message="form.errors.place_type_uuid" class="mt-2"/>
            </div>

             <div class="mt-4">
                <JetLabel for="status" value="Status" mandatory="true"/>
                 <jet-drop-down-widget
                     class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm h-11"
                     v-model="form.status"
                     :items="placeStatus"
                 />
                <JetInputError :message="form.errors.status" class="mt-2"/>
            </div>

        </div>
      </template>
      <template #footer>
        <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
        >Cancel</jet-secondary-button>
        <jet-button
            dusk="create_place"
            id="create_place"
            class="ml-4 justify-center"
            @click="create"
        >
          Add Place
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
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import colors from "tailwindcss/colors";

export default defineComponent({
    props: {
        locations: Array,
        placeTypes: Array,
        placeStatus: Array,
        selectedLocations: Array,
    },

    components: {
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        JetLabel,
        JetInput,
        JetInputError,
        JetDropDownWidget,
        JetTextarea,
    },

    setup(props) {
        const creating = ref(false);

        const form = useForm({
            name: null,
            description: null,
            location_uuid: null,
            place_type_uuid: null,
            status: null,
        })

        const startCreating = () => {
            creating.value = true;
        }

        const create = () => {
            form.post(route("admin.resources.places.store"), {
                onSuccess: (res) => {
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
            form,
        }
    },
});
</script>
