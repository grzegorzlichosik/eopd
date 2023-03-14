<template>
    <div>
        <div v-if="qrCode">
            <div class="mt-4 max-w-xl text-sm text-gray-600">
                <p class="font-semibold">
                    Two factor authentication is now enabled. Scan the following QR code
                    using your phone's authenticator application.
                </p>
            </div>

            <div class="mt-4" v-html="qrCode"></div>

            <div v-if="recoveryCodes.length > 0">
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        Store these recovery codes in a secure password manager. They can be
                        used to recover access to your account if your two factor
                        authentication device is lost.
                    </p>
                </div>

                <div
                    class="
            grid
            gap-1
            max-w-xl
            mt-4
            px-4
            py-4
            font-mono
            text-sm
            bg-gray-100
            rounded-lg
          "
                >
                    <div v-for="code in recoveryCodes" :key="code">
                        {{ code }}
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="mt-4">
                <jet-label for="code" value="Code"/>
                <jet-input
                    ref="code"
                    id="code"
                    type="text"
                    inputmode="numeric"
                    class="mt-1 block w-full"
                    v-model="form.code"
                    autofocus
                    autocomplete="one-time-code"
                />
                <jet-input-error
                    :message="form.errors?.confirmTwoFactorAuthentication?.code"
                    class="mt-2"
                />

                <div class="flex items-center justify-center mt-4">
                    <jet-button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Confirm
                    </jet-button>

                </div>
                <button
                    type="button" style="align-content: center"
                    class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                    @click="emailRecoveryCode"
                >
                    Email recovery code
                </button>

            </form>
            <jet-dialog-modal
                :show="codeMailed"
                @close="codeMailed = false"
            >
                <template #content>
                        We have emailed your recovery codes to your account email address. Please check your email.
                </template>

                <template #footer>
                    <jet-button @click="codeMailed = false">
                        Back
                    </jet-button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script>
import {defineComponent} from "vue";
import JetButton from "@/Components/Button.vue";
import JetConfirmsPassword from "@/Components/ConfirmsPassword.vue";
import JetDangerButton from "@/Components/DangerButton.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetInput from "@/Components/Input.vue";
import JetLabel from "@/Components/Label.vue";
import JetValidationErrors from "@/Components/ValidationErrors.vue";
import JetInputError from "@/Components/InputError.vue";
import NavLink from "../../../Components/NavLink";
import { usePage } from '@inertiajs/inertia-vue3'
import JetDialogModal from "@/Components/DialogModal.vue";

export default defineComponent({
    components: {
        NavLink,
        JetButton,
        JetConfirmsPassword,
        JetDangerButton,
        JetSecondaryButton,
        JetInput,
        JetLabel,
        JetValidationErrors,
        JetInputError,
        JetDialogModal
    },

    data() {
        return {
            enabling: false,
            disabling: false,
            qrCode: null,
            recoveryCodes: [],
            recovery: false,
            form: this.$inertia.form({
                code: "",
            }),
            codeMailed : false,
        };
    },

    mounted: function () {
        this.enableTwoFactorAuthentication();
    },
    methods: {
        enableTwoFactorAuthentication() {
            this.enabling = true;

            this.$inertia.post(
                "/user/two-factor-authentication",
                {},
                {
                    preserveScroll: true,
                    onSuccess: () =>
                        Promise.all([this.showQrCode(), this.showRecoveryCodes()]),
                    onFinish: () => (this.enabling = false),
                }
            );
        },

        showQrCode() {
            return axios.get("/user/two-factor-qr-code").then((response) => {
                this.qrCode = response.data.svg;
            });
        },

        showRecoveryCodes() {
            return axios.get("/user/two-factor-recovery-codes").then((response) => {
                this.recoveryCodes = response.data;
            });
        },

        emailRecoveryCode() {
           axios.get( route('mail.recovery.codes',{
                'uuid' : usePage().props.value.user.uuid,
                'code' : this.recoveryCodes
            }
            )).then((response) => {
               this.codeMailed = true;
            });

        },

        submit() {
            this.form.post(this.route("two-factor.confirm"));
        },
    },

    computed: {
        twoFactorEnabled() {
            return !this.enabling && this.$page.props.user.two_factor_enabled;
        },
    },
});
</script>
