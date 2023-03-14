<template>
    <Link
        class="mb-4 mt-4 ml-4 mr-4  sm:max-w-md shadow-md overflow-hidden sm:rounded-lg flex w-full justify-center"
        style="font-weight: bold;color: white"
    >
        <div class="pl-3 pr-3 px-6 py-4  flex-1">
            <div class="text-secondary card-header" style="font-size: 1.875rem;line-height: 2.25rem;">
                <slot name="title"/>
            </div>
            <div class="text-secondary card-headers text-xl text-center">
                <slot/>
            </div>
        </div>
    </Link>

</template>

<script>
import {defineComponent} from 'vue'
import Link from "@/Components/Link.vue";

export default defineComponent({
    components: {
        Link,
    },
    props: {
        type: {
            type: String,
            default: 'submit',
        },
    }
})
</script>
