<template>
    <AuthLayout title="Account Activation">
    <JetAuthenticationCard>
        <template #title>
            Account Activation
        </template>

        <div class="mb-4 text-sm text-gray-600">
            Resend Activation Link? No problem. Just let us know your email address and we will email you an activation link that will allow you to activate your account.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <JetLabel for="email" value="Email" />
                <JetInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
                <JetInputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                   Back to Login
                </Link>
                <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Send Account Activation Link
                </JetButton>
            </div>
        </form>
    </JetAuthenticationCard>
    </AuthLayout>
</template>
<script>
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetLabel from '@/Components/Label.vue';
import {defineComponent} from "vue";
import AuthLayout from '@/Layouts/AuthLayout.vue'

export default defineComponent({
    components: {
        AuthLayout,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetInputError,
        JetLabel,
        Head,
        Link
    },
    props: {
        status: String,
    },
    setup() {
        const form = useForm({
            email: '',
        });

        const submit = () => {
            form.post(route('resend.activation'));
        };

        return {
            form,
            submit
        }
    }
})
</script>
