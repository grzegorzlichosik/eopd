<template>
    <Head :title="title"/>
    <div class="bg-gray-100" >
        <nav class="bg-brand-500 border-b border-gray-100" ref="header">
            <!-- Primary Navigation Menu -->
            <div class="container mx-auto sm:px-2">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                                <JetApplicationMark class="block h-24 w-auto"/>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Page Content -->
        <main
            class="container mx-auto flex flex-col sm:justify-center items-center"
            :style="{ minHeight: bodyMinHeight, marginBottom: 0 }"
        >
            <slot/>
        </main>
        <footer>
            <JetFooter ref="footer"/>
        </footer>
    </div>
</template>

<script>
import { defineComponent, nextTick } from "vue";
import JetApplicationMark from "@/Components/ApplicationMark.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetFooter from "@/Components/AuthFooter";

export default defineComponent({
    props: {
        title: String,
    },

    components: {
        Head,
        JetApplicationMark,
        Link,
        JetFooter,
    },

    data() {
        return {
            bodyMinHeight: "100vh",
        };
    },

    watch: {
        bodyMinHeight: {
            handler: function () {
                nextTick(() => {
                    let header = this.$refs.header.clientHeight;
                    let footer = this.$refs.footer.$el.clientHeight;
                    this.bodyMinHeight = `calc(100vh - (${header}px + ${footer}px ))`;
                });
            },
            immediate: true,
        },
    },

    methods: {},
});

    window.when = window.when || {};
    window.when.license = 1234567890;
    window.when.random = Math.random();
    (function() {
    var wc = document.createElement('script'); wc.type = 'text/javascript'; wc.async = true;
    wc.src = '/widget.js?' + Math.random();
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wc, s);
})();

</script>
