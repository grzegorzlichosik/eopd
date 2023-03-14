<template>
    <AuthLayout title="Register">
        <JetAuthenticationCard v-if="message">
            <template #title>
                Congratulations
            </template>
            <div class="p-error">
                <div class="mb-4 text-sm text-gray-600" style="text-align: center;font-weight: bold">
                    We've sent you an email on how to activate your account.
                </div>

                <div style="text-align: center;">
                    <Link :href=" route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                        Return to Login
                    </Link>
                </div>


            </div>
        </JetAuthenticationCard>
        <JetAuthenticationCard v-else-if="!message">
            <template #title>
                Register
            </template>


            <form @submit.prevent="submit">

                <div>
                    <JetLabel for="name" value="Name" mandatory="true"/>
                    <JetInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <JetInputError class="mt-2" :message="form.errors.name"/>
                </div>

                <div class="mt-4">
                    <JetLabel for="email" value="Email" mandatory="true"/>
                    <JetInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                    />
                    <JetInputError class="mt-2" :message="form.errors.email"/>
                </div>

                <div class="mt-4">
                    <JetLabel for="phone_number" value="Phone Number" mandatory="true"/>
                    <vue-tel-input v-model="form.phone_number" name="phone_number" id="phone_number" required
                                   v-bind="bindProps" v-on:country-changed="countryChanged" style="border-radius: 0px;height:45px;--tw-border-opacity: 1;
    border-color: rgb(209 213 219 / var(--tw-border-opacity));"
                                   :preferredCountries="preferredCountries"
                    ></vue-tel-input>
                    <JetInputError class="mt-2" :message="form.errors.phone_number"/>
                </div>
                <div class="mt-4">
                    <JetLabel for="organisation_name" value="Organisation Name" mandatory="true"/>
                    <JetInput
                        id="organisation_name"
                        v-model="form.organisation_name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autocomplete="organisation_name"
                    />
                    <JetInputError class="mt-2" :message="form.errors.organisation_name"/>
                </div>

                <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                    <JetLabel for="terms">
                        <div class="flex items-center">
                            <JetCheckbox id="terms" v-model:checked="form.terms" name="terms" required/>

                            <div class="ml-2">
                                I agree to the <a target="_blank" :href="route('terms.show')"
                                                  class="underline text-sm text-gray-600 hover:text-gray-900">Terms of
                                Service</a> and <a target="_blank" :href="route('policy.show')"
                                                   class="underline text-sm text-gray-600 hover:text-gray-900">Privacy
                                Policy</a>
                            </div>
                        </div>
                        <JetInputError class="mt-2" :message="form.errors.terms"/>
                    </JetLabel>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                        Already registered?
                    </Link>

                    <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Register
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
import JetCheckbox from '@/Components/Checkbox.vue';
import JetTextarea from '@/Components/Textarea.vue';
import JetLabel from '@/Components/Label.vue';
import {defineComponent} from "vue";
import AuthLayout from '@/Layouts/AuthLayout.vue';

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
        JetTextarea,
        Head,
        Link
    },
    props: {
        status: String,
        message: String,
        preferredCountries: Array,
    },

    data() {
        return {
            bindProps: {
                mode: "international",
                placeholder: "Enter a phone number",
                maxLen: 25,
                validCharactersOnly: true,
                autoFormat: false,
            },
            form: useForm({
                name: '',
                email: '',
                phone_number: '',
                country_code: '',
                dial_code: "",
                organisation_name: '',
                terms: false,
            }),

            country: null,
            dialCode: null
        };

    },

    methods: {
        countryChanged(country) {
            this.country = country.iso2
            this.dialCode = country.dialCode
        },

        submit() {
            this.form.country_code = this.country;
            this.form.dial_code = this.dialCode;
            this.form.post(route('register'), {
                onSuccess: (res) => {
                    this.form.reset('name', 'email', 'phone_number', 'organisation_name', 'country_code', 'dial_code')
                },
            });
        }

    },

})
</script>
