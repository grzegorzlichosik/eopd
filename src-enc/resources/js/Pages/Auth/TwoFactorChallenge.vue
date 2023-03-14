<template>
    <AuthLayout title="Log in">
        <JetAuthenticationCard>
            <template #title>
                Enter your code
            </template>
            <div v-if="form.errors.account_locked" class="mb-4">
                <div class="font-medium font-semibold text-red-700">Whoops, something went wrong.</div>
                <ul class="mt-3 list-disc list-inside text-sm text-white bg-red-600 border border-red-600 text-white px-4 py-3">
                    <li>Your account has been locked. To reset your 2FA, please click <a
                        class="cursor-pointer inline-flex items-center px-1 py-1/2 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-800 active:bg-red-900 focus:outline-none focus:bg-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition"
                        @click.prevent="confirm2FAReset"> here</a></li>
                </ul>
            </div>

            <div class="mb-4 text-sm text-gray-600">
                <template v-if="! recovery">
                    Please confirm access to your account by entering the authentication code provided by your
                    authenticator
                    application.
                </template>

                <template v-else>
                    Please confirm access to your account by entering one of your emergency recovery codes.
                </template>
            </div>

            <form @submit.prevent="submit">
                <div v-if="! recovery">
                    <JetLabel for="code" value="Code"/>
                    <JetInput
                        id="code"
                        ref="codeInput"
                        v-model="form.code"
                        type="text"
                        inputmode="numeric"
                        class="mt-1 block w-full"
                        autofocus
                        autocomplete="one-time-code"
                    />
                    <JetInputError class="mt-2" :message="form.errors.code"/>
                </div>

                <div v-else>
                    <JetLabel for="recovery_code" value="Recovery Code"/>
                    <JetInput
                        id="recovery_code"
                        ref="recoveryCodeInput"
                        v-model="form.recovery_code"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="one-time-code"
                    />
                    <JetInputError class="mt-2" :message="form.errors.recovery_code"/>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                            @click.prevent="toggleRecovery">
                        <template v-if="! recovery">
                            Use a recovery code
                        </template>

                        <template v-else>
                            Use an authentication code
                        </template>
                    </button>

                    <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Log in
                    </JetButton>
                </div>
                <div class="flex justify-between mt-4">
                    <button
                        type="button"
                        class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                        @click.prevent="confirm2FAReset"
                    >
                        Reset 2FA
                    </button>
                </div>
            </form>
        </JetAuthenticationCard>
        <jet-confirmation-modal
            :show="confirming2FAReset"
            @close="confirming2FAReset = false"
        >
            <template #title>
                <div v-if="reset2FASuccess">Please check your email</div>
                <div v-else>
                    Important
                </div>
            </template>

            <template #content>
                <div v-if="reset2FASuccess">
                    Your Password and 2FA has been reset. We’ve emailed you a link to reset your password and 2 Factor
                    Authentication details.
                </div>
                <div v-else>
                    Please be aware that resetting 2FA will reset your password and 2FA credentials. Are you sure you
                    want to proceed?
                </div>
            </template>

            <template #footer v-if="reset2FASuccess">
                <nav-link :href="route('logout')"
                          method="post"
                          as="button"
                >
                    <jet-button>
                        Return to Login
                    </jet-button>
                </nav-link>

            </template>
            <template #footer v-else>
                <jet-secondary-button @click="confirming2FAReset = false">
                    Cancel
                </jet-secondary-button>
                <jet-danger-button
                    class="ml-3"
                    @click="reset2FA"
                    :class="{ 'opacity-25': reset2FASuccess }"
                    :disabled="reset2FASuccess"
                >
                    Continue
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </AuthLayout>
</template>

<script>
import {nextTick, ref, defineComponent} from 'vue';
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetLabel from '@/Components/Label.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import JetValidationErrors from "@/Components/ValidationErrors.vue";
import JetConfirmationModal from "@/Components/ConfirmationModal.vue";
import JetDangerButton from "@/Components/DangerButton.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import NavLink from "@/Components/NavLink.vue";

export default defineComponent({
    components: {
        AuthLayout,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetInputError,
        JetLabel,
        JetValidationErrors,
        Head,
        Link,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        NavLink
    },
    props: {
        canResetPassword: Boolean,
        status: String,
    },
    setup() {
        const recovery = ref(false);
        const confirming2FAReset = ref(false);
        const reset2FASuccess = ref(false);

        const form = useForm({
            code: '',
            recovery_code: '',
            is_recovery_mode: recovery
        });

        const recoveryCodeInput = ref(null);
        const codeInput = ref(null);

        const toggleRecovery = async () => {
            recovery.value ^= true;
            await nextTick();

            form.errors = false;

            if (recovery.value) {
                recoveryCodeInput.value.focus();
                form.code = '';
            } else {
                codeInput.value.focus();
                form.recovery_code = '';
            }
        };

        const submit = () => {
            form.post(route('two-factor.login'));
        };

        const confirm2FAReset = () => {
            confirming2FAReset.value = true;
        }

        const reset2FA = () => {
            axios.post(route("two-factor.reset")).then(function (response) {
                reset2FASuccess.value = true;
            })
        }

        return {
            confirming2FAReset,
            confirm2FAReset,
            reset2FA,
            reset2FASuccess,
            recovery,
            recoveryCodeInput,
            codeInput,
            toggleRecovery,
            form,
            submit,


        }
    }
})
</script>
