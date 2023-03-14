<template>
    <AuthLayout title="Log in">
        <JetAuthenticationCard>
            <template #title>
                    Secure Login
            </template>

            <jet-validation-errors class="mb-4"/>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <JetLabel for="email" value="Email"/>
                    <JetInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autofocus
                    />
                </div>

                <div class="mt-4">
                    <JetLabel for="password" value="Password"/>
                    <JetInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                    />
                </div>
                <div class="flex items-center justify-between mt-4">
                    <Link v-if="canResetPassword" :href="route('password.request')"
                          class="underline text-sm text-gray-600 hover:text-gray-900 flex items-center justify-end"
                          dusk="forgot-password"
                    >
                        Forgot your password?
                    </Link>
                    <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Submit
                    </JetButton>
                </div>
                <div class="flex justify-between">
                    <Link :href="route('resend.activation')"
                        class="underline text-sm text-gray-600 hover:text-gray-900 flex items-center justify-start mt-4">
                        Resend activation
                    </Link>
                </div>
            </form>
        </JetAuthenticationCard>
        <div class="flex items-center justify-center">
            <Link :href="route('register')"
                  class="underline text-sm text-gray-600 hover:text-gray-900 flex items-center justify-start mt-4">
                Register
            </Link>
        </div>
    </AuthLayout>
</template>

<script>
import {defineComponent} from "vue";
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetCheckbox from '@/Components/Checkbox.vue';
import JetLabel from '@/Components/Label.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import JetValidationErrors from "@/Components/ValidationErrors.vue";

export default defineComponent({
    components: {
        AuthLayout,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetInputError,
        JetCheckbox,
        JetLabel,
        JetValidationErrors,
        Head,
        Link
    },
    props: {
        canResetPassword: Boolean,
        status: String,
    },
    setup() {
        const form = useForm({
            email: '',
            password: '',
        });

        const submit = () => {
            form.transform(data => ({
                ...data,
                remember: form.remember ? 'on' : '',
            })).post(route('login'), {
                onFinish: () => form.reset('password'),
            });
        };

        return {
            form,
            submit
        }
    }
})
</script>
