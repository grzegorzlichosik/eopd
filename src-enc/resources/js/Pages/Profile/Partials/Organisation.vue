<template>
  <span>
    <span @click="startUpdating">
      <slot/>
    </span>
      <form>
    <jet-dialog-modal :show="updating" modal-id="user" @close="closeModal">
      <template #title> Edit Organisation Details </template>
      <template #content>
            <div class="col-span-6 sm:col-span-4 pb-2">
                <JetLabel for="organisation_name" value="Name" mandatory="true"/>
                <JetInput
                    id="name"
                    ref="name"
                    name="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autofocus
                />
                <JetInputError :message="form.errors.name" class="mt-2"/>
            </div>
           <div class="col-span-6 sm:col-span-4 pb-2" style="display:flex;flex-direction:column;flex:1">
                    <JetLabel for="phone_number" value="Phone Number" mandatory="true"/>
                    <vue-tel-input v-model="form.phone_number" name="phone_number" id="phone_number" required
                                   v-bind="bindProps" v-on:country-changed="countryChanged" style="border-radius: 0px;height:45px;--tw-border-opacity: 1;
    border-color: rgb(209 213 219 / var(--tw-border-opacity));"
                    ></vue-tel-input>
                    <JetInputError class="mt-2" :message="form.errors.phone_number"/>
                </div>
            <div class="col-span-6 sm:col-span-4  pb-2">
                <JetLabel for="file" value="Logo"/>
                <input type="file" name="file" v-on:change="onFileChange" accept=".jpg,.jpeg,.png"
                       class="inline-flex items-center px-4 py-2 bg-brand-300 border border-transparent rounded-md
                       font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 active:bg-brand-900
                        focus:outline-none focus:border-brand-900 focus:ring focus:ring-brand-300 disabled:opacity-25
                        transition w-full"/>
                  <div id="preview" v-if="form.file" class="mt-5 mb-5">
                      <img v-if="uploadedUrl" :src="uploadedUrl" alt="" height="100" width="150"/>
                      <img v-else :src="organisation.path" alt="" height="100" width="150"/>
                  </div>
                <JetInputError :message="form.errors.file" class="mt-2"/>
            </div>
            <div class="col-span-6 sm:col-span-4  pb-2">
                <JetLabel for="org_color" value="Header Color"/>
                <color-picker v-model="form.color" :inline="true"/>
                <span><div :style="{backgroundColor: '#'+form.color}" class="text-white text-center">Selected Color = #{{
                        form.color
                    }}</div></span>
                <JetInputError :message="form.errors.color" class="mt-2"/>
            </div>
      </template>
      <template #footer>
        <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
        >Cancel</jet-secondary-button>
        <jet-button
            dusk="edit_org"
            id="edit_org"
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
import {defineComponent, ref, reactive} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetLabel from "@/Components/Label.vue";
import JetInput from "@/Components/Input.vue";
import JetValidationErrors from "@/Components/ValidationErrors.vue";
import JetInputError from '@/Components/InputError.vue';
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import ColorPicker from 'primevue/colorpicker';


export default defineComponent({

    props: {
        organisation: Object
    },

    components: {
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        JetLabel,
        JetInput,
        JetValidationErrors,
        JetInputError,
        ColorPicker,
    },

    setup(props) {
        const updating = ref(false);
        let countryCode = ref(null);
        let dialCode = ref(null);
        let selected = 'background-color:' + usePage().props.value.user.organisation.header_mail_color
        const userOptions = reactive([]);
        const form = useForm({
            name: null,
            phone_number: null,
            country_code: null,
            dial_code: null,
            file: null,
            file_name: null,
            file_type: null,
            file_mimetype: null,
            color: null,
        })

        const uploadedUrl = ref(false);

        const startUpdating = () => {
            updating.value = true;
            form.name = usePage().props.value.user.organisation.name
            form.phone_number = usePage().props.value.user.organisation.phone_number
            form.color = usePage().props.value.user.organisation.header_mail_color
        }

        const update = () => {
            form.country_code = countryCode;
            form.dial_code = dialCode;
            form.post(route('organisation.update'), {
                preserveScroll: true,
                preserveState: page => Object.keys(page.props.errors).length,
                onSuccess: () => {
                    closeModal()
                }

            });
        }
        const countryChanged = (country) => {
            countryCode = country.iso2
            dialCode = country.dialCode
        }

        const closeModal = () => {
            form.reset();
            form.clearErrors();
            updating.value = false;
        }

        const onFileChange = (e) => {
            form.file = e.target.files[0];
            form.file_name = form.file.name.replace(/\.[^/.]+$/, '');
            form.file_type = form.file.type.replace(/(.*)\//g, '');
            form.file_mimetype = form.file.type;
            uploadedUrl.value = URL.createObjectURL(form.file);
            const fileReader = new FileReader();
            fileReader.onload = function(e) {
                form.file = fileReader.result;
            };
            fileReader.readAsDataURL(form.file);
        }

        return {
            bindProps: {
                mode: "international",
                placeholder: "Enter a phone number",
                maxLen: 25,
                validCharactersOnly: true,
                autoFormat: true,
            },
            startUpdating,
            update,
            closeModal,
            onFileChange,
            countryChanged,
            selected,
            updating,
            form,
            userOptions,
            countryCode,
            dialCode,
            uploadedUrl,
        }
    },
});
</script>
<style>
#preview {
    display: flex;
    justify-content: center;
    align-items: center;
}

#preview img {
    max-width: 100%;
    max-height: 500px;
}
</style>
