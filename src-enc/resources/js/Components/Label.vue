<script setup>
defineProps({
    value: String,
    mandatory: {
        Boolean,
        default: false
    }
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-700">
        <span v-if="value">{{ value }}</span>
        <span v-if="mandatory" style="color: red">&nbsp;*</span>
        <span v-else><slot /></span>
    </label>
</template>
