<template>
    <textarea
        class="text-sm mt-1 block w-full pt-1 pb-1 pl-3 pr-3 border-gray-300 focus:border-indigo-300  focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
        :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="textarea">
    </textarea>
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
