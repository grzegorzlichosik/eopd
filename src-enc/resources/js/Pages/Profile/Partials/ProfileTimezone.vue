<template>
  <span>
    <span @click="startUpdating">
      <slot/>
    </span>
      <form>
    <jet-dialog-modal :show="updating" modal-id="user" @close="closeModal">
      <template #title> Edit Profile </template>
      <template #content>
        <div class="grid grid-cols-1 gap-2 p-6">
            <div class="mt-4">
                <JetLabel for="name" value="Name" mandatory="true"/>
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
            <div class="mt-4">
                <JetLabel for="name" value="Timezone" mandatory="true"/>
                    <Select2 v-model="form.timezone"
                             :options="timezoneOptions"
                             :selected="$page.props.user.timezone ? $page.props.user.timezone : Intl.DateTimeFormat().resolvedOptions().timeZone"
                    />

                <JetInputError :message="form.errors.timezone" class="mt-2"/>
            </div>


        </div>
      </template>
      <template #footer>
        <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
        >Cancel</jet-secondary-button>
        <jet-button
            dusk="edit_user_name"
            class="ml-4 justify-center"
            @click="update"
        >
          Save
        </jet-button>
      </template>
    </jet-dialog-modal>
          </form>
  </span>
</template>

<script>
import {defineComponent, ref, reactive, onMounted} from "vue";
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
import Select2 from 'vue3-select2-component';

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
        JetInputError,
        Select2,
    },

    setup(props) {
        const updating = ref(false);
        const userOptions = reactive([]);
        const timezoneOptions = reactive([]);
        const form = useForm({
            name: null,
            timezone: null,
        })

        const startUpdating = () => {
            updating.value = true;
            form.name = usePage().props.value.user.name;
            let timezone =  Intl.DateTimeFormat().resolvedOptions().timeZone ??  'UTC'
            form.timezone = usePage().props.value.user.timezone ?? timezone
        }

        const update = () => {
            form.put(route('user.update-profile'), {
                    onSuccess: (res) => {
                    closeModal();
                },
                onError: () => {
                    if (form.errors.name) {
                        form.reset('name');
                    }
                },
            });
        }

        const closeModal = () => {
            form.reset();
            form.clearErrors();
            updating.value = false;
        }

        onMounted(() => {
            axios.get(route('user.timezones')
            ).then(response => {
                let timeZone = response.data
                timeZone.forEach((item) =>
                    timezoneOptions.push({
                        id: item,
                        text: item,
                    })
                );
            })


        })

        return {
            startUpdating,
            update,
            closeModal,
            updating,
            timezoneOptions,
            form,
            userOptions,
        }
    },
});
</script>
<style>
.select2 {
    width: 100% !important;
    border-radius: 0px !important;

}
.select2 select2-container select2-container--default {
    width: 100% !important;
    --tw-border-opacity: 1;
    border-color: rgb(209 213 219 / var(--tw-border-opacity)) !important;
    height: 2.75rem !important;
}
.select2-selection select2-selection--single {
    height: 2.75rem !important;
    border-radius: 0;
}
</style>
