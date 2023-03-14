<template>
    <button :type="type"
            class="
            shadow
            text-secondary
            inline-flex
            items-center
            px-2
            py-1
            border
            border-transparent
            rounded-md
            font-semibold
            text-xs text-white
            tracking-widest
            focus:outline-none
            focus:border-gray-900
            focus:ring
            focus:ring-gray-300
            disabled:opacity-25
            transition">
        <slot></slot>
    </button>
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
    props: {
        type: {
            type: String,
            default: 'submit',
        },
    }
})
</script>
