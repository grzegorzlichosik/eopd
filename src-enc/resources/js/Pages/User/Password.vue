<template>
  <app-layout title="Profile">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Password
      </h2>
    </template>

    <div>
      <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div v-if="$page.props.jetstream.canUpdatePassword">
          <update-password-form class="mt-10 sm:mt-0" />
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
import UpdatePasswordForm from "@/Pages/User/Partials/UpdatePasswordForm.vue";

export default defineComponent({
  props: ["sessions"],

  components: {
    AppLayout,
    JetSectionBorder,
    UpdatePasswordForm,
  },
});
</script>
