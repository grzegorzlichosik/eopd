<template>
  <auth-layout title="Set Two-factor">
    <jet-authentication-card class="my-4">
      <template #title>Two Factor Authentication</template>

      <set-two-factor-authentication-form class="mt-10 sm:mt-0" />
      <template #link>
        <nav-link
          :href="route('logout')"
          method="post"
          as="button"
          class="underline text-sm text-gray-600 hover:text-gray-900"
          >Log Out
        </nav-link>
      </template>
    </jet-authentication-card>
  </auth-layout>
</template>
<script>
import { defineComponent } from "vue";
import JetAuthenticationCard from "@/Components/AuthenticationCard.vue";
import SetTwoFactorAuthenticationForm from "@/Pages/User/Partials/SetTwoFactorAuthenticationForm.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import NavLink from "@/Components/NavLink.vue";

export default defineComponent({
  components: {
    JetAuthenticationCard,
    SetTwoFactorAuthenticationForm,
    AuthLayout,
    NavLink,
  },

  data() {
    return {};
  },
});
</script>
