<template>
    <JetFormSection @submitted="updatePassword">
        <template #title>
            User Profile
        </template>

        <template #description>
            Update your profile name and timezone here.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4" style="display:flex;flex-direction:column;flex:1">
                <JetLabel for="user_name" value="Name"/>
                <span>
                    {{ $page.props.user.name}}
                </span>
            </div>
            <div class="col-span-6 sm:col-span-4" style="display:flex;flex-direction:column;flex:1">
                <JetLabel for="timezone" value="Timezone"/>
                <span>
                    {{ $page.props.user.timezone ? $page.props.user.timezone : Intl.DateTimeFormat().resolvedOptions().timeZone }}

                </span>
            </div>

            <div class="col-span-6" style="text-align:end">
                <profile-timezone>
                    <JetButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                               id="edit_profile"
                               dusk="edit_profile">
                        Edit
                    </JetButton>
                </profile-timezone>

            </div>
            <div class="col-span-6">
            <hr>
            </div>
        <div class="col-span-6 sm:col-span-4" >
            <div style="display:flex;align-items:center">
                <JetLabel for="Role" value="Roles"/>
            </div>
            <div>
                <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-blue-300 mb-1 w-fit"
                     v-if="$page.props.user.is_super_admin">
                    Super&nbsp;Administrator
                </div>
                <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-green-300 mb-1 w-fit"
                     v-if="$page.props.user.is_admin">
                    Administrator
                </div>
                <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-yellow-300 mb-1 w-fit"
                     v-if="$page.props.user.is_agent">
                    Agent
                </div>
                <div class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-lime-300 w-fit"
                     v-if="$page.props.user.is_developer">
                    Developer
                </div>
            </div>

        </div>
            </template>

    </JetFormSection>

</template>

<script setup>
import {onMounted, ref} from 'vue';
import {useForm, usePage} from '@inertiajs/inertia-vue3';
import JetActionMessage from '@/Components/ActionMessage.vue';
import JetButton from '@/Components/Button.vue';
import JetFormSection from '@/Components/FormSection.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetLabel from '@/Components/Label.vue';
import ProfileTimezone from "./ProfileTimezone";

const userName = ref(null);

let userRole = []

const form = useForm({
    user_name: usePage().props.value.user.name
});

</script>
