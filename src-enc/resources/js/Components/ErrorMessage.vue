<script setup>
const props = defineProps({
    message: String,
});

</script>

<template>
    <div class="w-full p-5 bg-red-200 text-red-800" v-html="message"></div>
</template>
