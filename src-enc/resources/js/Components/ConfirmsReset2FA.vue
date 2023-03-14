<script setup>
import {ref} from 'vue';
import JetButton from './Button.vue';
import JetSecondaryButton from './SecondaryButton.vue';
import JetDangerButton from './DangerButton.vue';
import JetConfirmationModal from './ConfirmationModal.vue';
import NavLink from './NavLink.vue';

const reset2FASuccess = ref(false);

defineProps({
    showReset: Boolean,
});

const reset2FA = () => {
    axios.post(route("user-two-factor.reset")).then(function (response) {
        reset2FASuccess.value = true;
    })
}

</script>

<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot/>
        </span>

        <JetConfirmationModal :show="showReset"
                              @close="showReset = false">
           <template #title>
                <div v-if="reset2FASuccess">Please check your email</div>
                <div v-else>
                    Important
                </div>
            </template>


           <template #content>
                <div v-if="reset2FASuccess">
                    Your Password and 2FA has been reset. We’ve emailed you a link to reset your password and 2 Factor Authentication details.
                </div>
                <div v-else>
                    Please be aware that resetting 2FA will reset your password and 2FA credentials. Are you sure you
                    want to proceed?
                </div>
            </template>

            <template #footer v-if="reset2FASuccess">
                <NavLink :href="route('logout')"
                         method="post"
                         as="button"
                ><jet-button>
                    Return to Login</jet-button></NavLink>

            </template>
            <template #footer v-else>
                <JetSecondaryButton @click="showReset = false">
                    Cancel
                </JetSecondaryButton>
                <JetDangerButton
                    class="ml-3"
                    @click="reset2FA"
                    :class="{ 'opacity-25': reset2FASuccess }"
                    :disabled="reset2FASuccess"
                >
                    Continue
                </JetDangerButton>
            </template>
        </JetConfirmationModal>
    </span>
</template>
