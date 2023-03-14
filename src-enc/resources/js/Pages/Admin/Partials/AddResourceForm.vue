<template>
  <span>
    <span @click="startCreating">
      <slot/>
    </span>
      <form>
    <jet-dialog-modal :show="creating" modal-id="user" @close="closeModal">
      <template #title> Add Place </template>
      <template #content>
        <div class="grid grid-cols-1 gap-2 p-6">

            <div class="mt-4">
                <JetLabel for="roles" value="Place" mandatory="true"/>
            </div>
            <span class="p-fluid">
                <AutoComplete
                    ref="autoComplete"
                    :multiple="true" v-model="form.selected_flows" name="selected_flows"
                    :suggestions="filteredPlaces" @complete="searchPlace($event)" optionLabel="name"
                    :minLength="3" placeholder="Minimum 3 characters required to search"
                    emptySearchMessage="No results found"
                />
                <JetInputError :message="form.errors.selected_flows" class="mt-2"/>
            </span>

        </div>
      </template>
      <template #footer>
        <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
        >Cancel</jet-secondary-button>
        <jet-button
            dusk="create_place"
            class="ml-4 justify-center"
            @click="create"
        >
          Add
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
    props: {
        flow: Object
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
        const creating = ref(false);
        const filteredPlaces = ref([]);

        const form = useForm({
            selected_flows: null,
        })

        const autoComplete = ref(null);

        const startCreating = () => {
            creating.value = true;
            setTimeout(() => {
                autoComplete.value.$refs.focusInput.focus()
            }, 100);
        }

        const create = () => {
            form.post(route("admin.flows.place.store", {uuid: props.flow.uuid}), {
                onSuccess: (res) => {
                    closeModal();
                },
            });
        }

        const onChange = (event) => {
            filteredPlaces.value = event.value;
        }

        const searchPlace = (event) => {
            setTimeout(() => {
                let selectedUsers = form.selected_flows ? form.selected_flows : [];
                let uuids = [];
                selectedUsers.forEach((item, index) => {
                    uuids.push(item.uuid)
                })
                axios.get(route('admin.flows.place.search', {
                    uuid: props.flow.uuid,
                    search: event.query,
                    selected: uuids
                })).then(response => {
                    filteredPlaces.value = response.data.length > 0
                        ? response.data
                        : [{
                            'name': 'No results found',
                            'value': 'No results found'
                        }];
                })
            }, 250);
        }

        const closeModal = () => {
            form.reset();
            form.clearErrors();
            creating.value = false;
        }

        return {
            filteredPlaces,
            onChange,
            searchPlace,
            startCreating,
            create,
            closeModal,
            creating,
            form,
            autoComplete,
        }
    },
});
</script>

<style>
.p-autocomplete input:focus {
    box-shadow: none !important;
}

.p-autocomplete:not(.p-disabled).p-focus .p-autocomplete-multiple-container {
    --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
    --tw-border-opacity: 1;
    border-color: #4F46E5;
    border-radius: 0;
}

</style>
