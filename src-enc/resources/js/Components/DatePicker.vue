<template>
    <input type="date"
           class="text-sm mt-1  pt-1 pb-1 pl-3 pr-3 block w-full border-border focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
           :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input">
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
    props: ['modelValue'],

    emits: ['update:modelValue'],

    methods: {
        focus() {
            this.$refs.input.focus()
        }
    }
})
</script>

<style scoped>

</style>
