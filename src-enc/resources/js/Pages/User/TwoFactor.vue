<template>
  <app-layout title="Two Factor">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Two Factor
      </h2>
    </template>

    <div>
      <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
          <two-factor-authentication-form class="mt-10 sm:mt-0" />
          <jet-section-border />
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetSectionBorder from "@/Components/SectionBorder.vue";
import TwoFactorAuthenticationForm from "@/Pages/User/Partials/TwoFactorAuthenticationForm.vue";

export default defineComponent({
  props: ["sessions"],

  components: {
    AppLayout,
    JetSectionBorder,
    TwoFactorAuthenticationForm,
  },
});
</script>
