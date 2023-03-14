<template>
    <div class="mt-4 flex justify-center">
        <Component
                :is="link.url ? 'a':'span'"
                class="pt-2 pb-2 pl-4 pr-4 border"
                v-for="link in paginationList"
                :href="link.url"
                v-html="link.label"
                :class="{'text-gray-400': !link.url, 'text-primary border-primary': link.active }"
        />
    </div>
</template>

<script>
    import {defineComponent} from "vue";
    import {Link} from '@inertiajs/inertia-vue3';

    export default defineComponent({
        props:  {
            paginationList : Array
        },
        component: {
            Link,
        },
    })
</script>

<style scoped>

</style>
