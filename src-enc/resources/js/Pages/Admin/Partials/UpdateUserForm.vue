<template>
  <span>
    <span @click="startUpdating">
      <slot/>
    </span>
    <jet-dialog-modal :show="updating" modal-id="user" @close="closeModal">
      <template #title> Edit User: {{ user.name }} </template>
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
                  dusk="edit-name"
              />
                <JetInputError :message="form.errors.name" class="mt-2"/>
            </div>
            <div class="mt-4">
              <jet-label for="email" value="Email" mandatory="true"/>
              <jet-input
                  id="email"
                  v-model="form.email"
                  class="mt-1 block w-full"
                  type="email"
                  :class="{ 'disabled:opacity-25': user.email_verified_at }"
                  :disabled="user.email_verified_at"
              />
                <JetInputError :message="form.errors.email" class="mt-2"/>
            </div>
            <div class="mt-4">
                <JetLabel for="roles" value="Roles" mandatory="true"/>
                <MultiSelect
                    id="roles"
                    v-model="form.roles"
                    :options="rolesOptions"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm mt-1 block w-full"
                    name="roles"
                    :showToggleAll="false"
                    optionLabel="name"
                    optionValue="value"
                    placeholder="Select Roles"
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
            dusk="update_user"
            class="ml-4 justify-center"
            @click="update"
        >
          Update
        </jet-button>
      </template>
    </jet-dialog-modal>
  </span>
</template>

<script>
import {defineComponent, onMounted, ref, reactive, computed} from "vue";
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
    props: {
        user: {
            type: Object,
            required: true,
        }
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
    },

    setup(props) {
        const updating = ref(false);
        const selectedRoles = ref(null);
        const rolesOptions = computed(() => {
            let roles = usePage().props.value.roles

            if (!usePage().props.value.user.is_super_admin) {
                roles = roles.filter(function (item, index, arr) {
                    return item.value != 'is_super_admin';
                })
            }
            return roles;
        });


        const form = useForm({
            name: null,
            email: null,
            roles: [],
        })

        const startUpdating = () => {
            form.name = props.user.name;
            form.email = props.user.email;
            form.roles = getUserRoles();
            updating.value = true;
        }

        const getUserRoles = () => {
            let result = [];
            for (let key of ['is_super_admin', 'is_admin', 'is_agent', 'is_developer']) {
                if (props.user[key]) {
                    result.push(key);
                }
            }

            /**
             * Only Super Admin can set is_super_admin flag
             */
            if (!usePage().props.value.user.is_super_admin) {
                result = result.filter(function (value, index, arr) {
                    return value != 'is_super_admin';
                })
            }

            return result;
        }

        const update = () => {
            form.transform((data) => {
                if (props.user.email_verified_at) {
                    delete data['email'];
                }
                return data;
            })
                .put(route("admin.users.update", {'uuid': props.user.uuid}), {
                    onSuccess: (res) => {
                        form.reset();
                        closeModal();
                    },
                });
        }

        const closeModal = () => {
            usePage().props.value.errors = {};
            form.clearErrors()
            updating.value = false;
        }

        return {
            startUpdating,
            update,
            closeModal,
            updating,
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
