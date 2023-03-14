<template>
  <span>
    <span @click="startUpdating">
      <slot/>
    </span>
      <form>
    <jet-dialog-modal :show="creating" modal-id="place" @close="closeModal">
      <template #title> Edit Place: {{ place.uuid }} </template>
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

           <div class="mt-4" v-if="place.place_type_label === 'resourced'">
                <JetLabel for="email" value="Email"/>
                <jet-input
                    v-model="form.email"
                    autocomplete="email"
                    disabled
                    class="mt-1 block w-full bg-blue-100"
                    type="email"
                />
                <JetInputError :message="form.errors.email" class="mt-2"/>
            </div>

             <div class="mt-4">
                <JetLabel for="description" value="Description" mandatory="true"/>
                <JetTextarea
                    dusk="edit_place_description"
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
                       :selected="place.location_uuid"
                   />
                <JetInputError :message="form.errors.location" class="mt-2"/>
            </div>

             <div class="mt-4" v-if="place.place_type_label === 'open'">
                <JetLabel for="type" value="Type" mandatory="true"/>
                 <jet-drop-down-widget
                     class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm h-11"
                     v-model="form.place_type_uuid"
                     :items="placeTypes"
                     :selected="place.place_type_uuid"
                 />
                <JetInputError :message="form.errors.type" class="mt-2"/>
            </div>

             <div class="mt-4">
                <JetLabel for="status" value="Status" mandatory="true"/>
                 <jet-drop-down-widget
                     class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm h-11"
                     v-model="form.status"
                     :items="placeStatus"
                     :selected="place.tsm_current_state"
                 />
                <JetInputError :message="form.errors.status" class="mt-2"/>
            </div>

        </div>
      </template>
      <template #footer>
        <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
        >Cancel</jet-secondary-button>
        <jet-button
            dusk="submit_edit_place"
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
import {useForm, usePage} from "@inertiajs/inertia-vue3";

export default defineComponent({
    props: {
        place: Object,
        locations: Array,
        placeTypes: Array,
        placeStatus: Array,
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
            name: '',
            description: '',
            location_uuid: '',
            place_type_uuid: '',
            status: '',
        })

        const startUpdating = () => {
            creating.value = true;
            form.name = props.place.name;
            form.description = props.place.description;
            form.location_uuid = props.place.location_uuid;
            form.place_type_uuid = props.place.place_type_uuid;
            form.status = props.place.tsm_current_state;
        }

        const update = () => {
            form.put(route('admin.resources.places.update', {uuid: props.place.uuid}), {
                preserveState: page => Object.keys(page.props.errors).length,
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
            startUpdating,
            update,
            closeModal,
            creating,
            form,
        }
    },
});
</script>
