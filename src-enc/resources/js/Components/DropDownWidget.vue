<template>
    <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
            class="text-sm pt-1 pb-1 pl-3 pr-3 mt-1 w-full border-border focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm">
        <option value="" :disabled="emptyDisabled">{{ emptyLabel }}</option>
        <option v-for="item in items" :value="item.value">{{ item.name }}</option>
    </select>
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
    props: {
        modelValue: String,
        items: Array,
        emptyDisabled: {
            type: Boolean,
            default: true,
        },
        emptyLabel: {
            default: 'Please Select',
        },
        getItem: Function,
    },

    emits: ['update:modelValue'],

    methods: {
        focus() {
            this.$refs.input.focus()
        }
    },
    data() {
        return {
            // model: '',
        }
    },
})
</script>

<style scoped>

</style>
