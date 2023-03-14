<template>
  <span>
    <span @click="startConfirming">
      <slot />
    </span>

    <jet-dialog-modal :show="confirming" @close="closeModal">
      <template #title>
        {{ title }}
      </template>

      <template #content v-html="content">
        <span v-html="content" />
      </template>

      <template #footer>
        <jet-secondary-button @click="closeModal">
          Cancel
        </jet-secondary-button>

        <jet-button dusk="confirm-button"
          class="ml-3"
          @click="confirm"
          :class="[form.processing ? 'opacity-25': '', buttonClass]"
          :disabled="form.processing"
        >
          {{ button }}
        </jet-button>
      </template>
    </jet-dialog-modal>
  </span>
</template>

<script>
import { defineComponent } from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetLabel from "@/Components/Label.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

export default defineComponent({
  emits: ["confirmed"],

  props: {
    title: {
      default: "Confirm",
    },
    content: {
      default: "Please Confirm",
    },
    button: {
      default: "Confirm",
    },
    buttonClass: {
      default: "",
    },
  },

  components: {
    JetButton,
    JetDialogModal,
    JetSecondaryButton,
    JetLabel,
    ResponsiveNavLink,
  },

  data() {
    return {
      confirming: false,
      form: {
        error: "",
      },
    };
  },

  methods: {
    startConfirming() {
      this.confirming = true;
    },

    confirm() {
      this.closeModal();
      this.$nextTick(() => this.$emit("confirmed"));
    },

    closeModal() {
      this.confirming = false;
    },
  },
});
</script>
