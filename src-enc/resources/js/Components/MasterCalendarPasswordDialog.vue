<template>
  <span>
    <span @click="startPasswordShowing">
      <slot/>
    </span>

    <jet-dialog-modal :show="showing" @close="closeModal">
      <template #title>
          Master Calendar Details for organisation: <strong>{{ organisation.name }}</strong>
      </template>

      <template #content>
          <strong>Email:</strong> {{content.email}}<br>
          <strong>Password:</strong> {{content.password}}
          <jet-button class="ml-2" @click="copyPassword(content.password)" title="Click here to copy password">
              <i class="fa fa-copy"></i>
          </jet-button>
          <br>
          <span v-if="copied" class="text-green-600">
              Password has been copied to clipboard.
          </span>
      </template>

      <template #footer>
        <jet-secondary-button @click="closeModal">
          Close
        </jet-secondary-button>
      </template>
    </jet-dialog-modal>
  </span>
</template>

<script>
import {defineComponent} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetLabel from "@/Components/Label.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

export default defineComponent({
    props: {
        organisation: Object,
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
            showing: false,
            copied: false,
            content: '',
        };
    },

    methods: {
        startPasswordShowing() {
            return axios.get(route('platform.organisations.master_calendar.show', {uuid: this.organisation.uuid})).then(response => {
                this.content = response.data;
                this.showing = true;
            });
        },

        async copyPassword(password) {
            try {
                await navigator.clipboard.writeText(password);
                this.copied = true;
            } catch ($e) {
                alert('Cannot copy');
            }
        },

        confirm() {
            this.closeModal();
            this.$nextTick(() => this.$emit("confirmed"));
        },

        closeModal() {
            this.showing = false;
        },
    },
});
</script>
