<template>
    <div v-if="hasErrors">
        <div class="font-medium font-semibold text-red-700">Whoops, something went wrong.</div>
        <ul class="mt-3 list-disc list-inside text-sm text-white bg-red-600 border border-red-600 text-white px-4 py-3">
            <li v-for="(error, key) in errors" :key="key" v-html="error"></li>
        </ul>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  computed: {
    errors() {
      return this.$page.props.errors;
    },

    hasErrors() {
      return Object.keys(this.errors).length > 0;
    },
  },
});
</script>
