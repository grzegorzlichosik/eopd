<template>
  <div>
    <button :class="classes" class="w-full text-left" v-if="as == 'button'">
      <slot></slot>
    </button>

    <InertiaLink :href="href" :class="classes" v-else>
      <slot />
    </InertiaLink>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { Link as InertiaLink } from "@inertiajs/inertia-vue3";

export default defineComponent({
  components: {
    InertiaLink,
  },
  props: ["href", "active", "as"],

  computed: {
    classes() {
      return this.active
        ? "flex-1 items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-button outline-none  transition"
        : "flex-1 items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-button outline-none   focus:outline-none focus:text-gray-700  transition";
    },
  },
});
</script>
