<template>
  <span>
    <span @click="startCreating">
      <slot/>
    </span>
    <jet-dialog-modal :show="creating" modal-id="user" @close="closeModal">
      <template #title> Create User </template>
      <template #content>
        <div class="grid grid-cols-1 gap-2 p-6">
          <form>
            <div>
              <jet-label for="name" value="Name" mandatory="true"/>
              <jet-input
                  id="name"
                  v-model="form.name"
                  autocomplete="name"
                  autofocus
                  class="mt-1 block w-full"
                  type="text"
              />
                <JetInputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="mt-4">
              <jet-label for="email" value="Email" mandatory="true"/>
              <jet-input
                  id="email"
                  v-model="form.email"
                  class="mt-1 block w-full"
                  type="email"
              />
                <JetInputError :message="form.errors.email" class="mt-2"/>
            </div>
            <div class="mt-4">
                <JetLabel for="roles" value="Roles" mandatory="true"/>
                <MultiSelect
                    id="roles"
                    v-model="form.roles" :options="rolesOptions"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm mt-1 block w-full  "
                    name="roles"
                    :showToggleAll="false"
                    optionLabel="name"
                    optionValue="value"
                    placeholder="Select Roles"
                    dusk="user_roles_selector"
                    style="border-radius: 0;height: 45px;"/>
                <JetInputError :message="form.errors.roles" class="mt-2"/>
            </div>
          </form>
        </div>
      </template>
      <template #footer>
        <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
        >Cancel</jet-secondary-button>
        <jet-button
            dusk="create_user"
            class="ml-4 justify-center"
            @click="create"
        >
          Add New User
        </jet-button>
      </template>
    </jet-dialog-modal>
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

export default defineComponent({
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
    },

    setup() {
        const creating = ref(false);
        const selectedRoles = ref(null);
        const rolesOptions = reactive([]);

        const form = useForm({
            name: "",
            email: "",
            roles: null,
        })

        const startCreating = () => {
            creating.value = true;
        }

        const create = () => {
            form.post(route("admin.users.create"), {
                onSuccess: (res) => {
                    form.reset();
                    closeModal();
                },
            });
        }

        const closeModal = () => {
            usePage().props.value.errors = {};
            creating.value = false;
        }

        onMounted(() => {
            usePage().props.value.roles.forEach((item) =>
                rolesOptions.push({
                    name: item.name,
                    value: item.value,
                })
            );
        })

        return {
            startCreating,
            create,
            closeModal,
            creating,
            selectedRoles,
            form,
            rolesOptions
        }
    },
});
</script>
<style>
.p-checkbox .p-checkbox-box.p-highlight {
    border-color: #002D28;
    background: #002D28;
}

.p-multiselect-item p-highlight {
    color: #002D28;
}

.p-multiselect p-component {
    height: 45px;
}

.p-multiselect:not(.p-disabled).p-focus {
    border-width: 0px;
    --tw-border-opacity: 1;
    --tw-ring-opacity: 0.5;
    border-color: rgb(165 180 252 / var(--tw-border-opacity));
    opacity: 0.5;
    --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color);
    box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
    --tw-border-opacity: 1;
    border-color: rgb(165 180 252 / var(--tw-border-opacity));
}


</style>
