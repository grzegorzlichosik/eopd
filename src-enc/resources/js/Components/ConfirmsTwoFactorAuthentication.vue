<template>
  <span dusk="`confirm-two-factor-modal`">
    <span @click="startConfirmingTwoFactorAuthentication">
      <slot />
    </span>

    <jet-dialog-modal
      :show="confirmingTwoFactorAuthentication"
      @close="closeModal"
    >
      <template #title>
        {{ title }}
      </template>

      <template #content>
        {{ content }}

        <div class="mt-4">
          <div v-if="!recovery">
            <jet-label for="code" value="Code" />
            <jet-input
              ref="code"
              id="code"
              type="text"
              inputmode="numeric"
              class="mt-1 block w-full"
              v-model="form.code"
              autofocus
              autocomplete="one-time-code"
            />
            <jet-input-error :message="form.error" class="mt-2" />
          </div>

          <div v-else>
            <jet-label for="recovery_code" value="Recovery Code" />
            <jet-input
              ref="recovery_code"
              id="recovery_code"
              type="text"
              class="mt-1 block w-full"
              v-model="form.recovery_code"
              autocomplete="one-time-code"
            />
            <jet-input-error :message="form.error" class="mt-2" />
          </div>

          <div class="flex-column">
            <div class="flex items-center justify-center">
              <responsive-nav-link
                as="button"
                @click.prevent="toggleRecovery"
                id="toggle"
              >
                <template v-if="!recovery"> Use a recovery code </template>

                <template v-else> Use an authentication code </template>
              </responsive-nav-link>
            </div>
          </div>
        </div>
      </template>

      <template #footer>
        <jet-secondary-button @click="closeModal">
          Cancel
        </jet-secondary-button>

        <jet-button
          class="ml-3"
          @click="confirmTwoFactorAuthentication"
          :class="{ 'opacity-25': form.processing }"
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
import JetButton from "./Button.vue";
import JetDialogModal from "./DialogModal.vue";
import JetInput from "./Input.vue";
import JetInputError from "./InputError.vue";
import JetSecondaryButton from "./SecondaryButton.vue";
import JetLabel from "./Label.vue";
import ResponsiveNavLink from "./ResponsiveNavLink.vue";

export default defineComponent({
  emits: ["confirmed"],

  props: {
    title: {
      default: "Confirm Two Factor Authentication",
    },
    content: {
      default:
        "For your security, please confirm your Two Factor Authentication to continue.",
    },
    button: {
      default: "Confirm",
    },
  },

  components: {
    JetButton,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetSecondaryButton,
    JetLabel,
    ResponsiveNavLink,
  },

  data() {
    return {
      recovery: false,
      confirmingTwoFactorAuthentication: false,
      form: {
        code: "",
        recovery_code: "",
        error: "",
      },
    };
  },

  methods: {
    startConfirmingTwoFactorAuthentication() {
      axios.get(route("two-factor.confirmation")).then((response) => {
        if (response.data.confirmed) {
          this.$emit("confirmed");
        } else {
          this.confirmingTwoFactorAuthentication = true;

          setTimeout(() => {
            if (this.recovery) {
              this.$refs.recovery_code.focus();
            } else {
              this.$refs.code.focus();
            }
          }, 250);
        }
      });
    },

    confirmTwoFactorAuthentication() {
      this.form.processing = true;

      axios
        .post(route("user-two-factor.confirm"), {
          code: this.form.code,
          recovery_code: this.form.recovery_code,
        })
        .then(() => {
          this.form.processing = false;
          this.closeModal();
          this.$nextTick(() => this.$emit("confirmed"));
        })
        .catch((error) => {
          this.form.processing = false;
          this.form.error = error.response.data.errors.code[0];
          if (this.recovery) {
            this.$refs.recovery_code.focus();
          } else {
            this.$refs.code.focus();
          }
        });
    },

    toggleRecovery() {
      this.recovery ^= true;

      this.$nextTick(() => {
        if (this.recovery) {
          this.$refs.recovery_code.focus();
          this.form.code = "";
        } else {
          this.$refs.code.focus();
          this.form.recovery_code = "";
        }
      });
    },

    closeModal() {
      this.confirmingTwoFactorAuthentication = false;
      this.form.code = "";
      this.form.recovery_code = "";
      this.form.error = "";
    },
  },
});
</script>
