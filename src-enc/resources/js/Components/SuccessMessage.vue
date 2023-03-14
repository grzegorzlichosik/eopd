<script setup>
const props = defineProps({
    message: String,
});

</script>

<template>
    <div class="w-full p-5 bg-green-200 text-green-800" v-html="message"></div>
</template>
