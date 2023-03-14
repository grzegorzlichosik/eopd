<template>
    <MultiSelect
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm mt-1 block w-full  "
         style="border-radius: 0;height: 45px !important;"
       />

</template>
<script>
import {defineComponent} from "vue";
import MultiSelect from 'primevue/multiselect';

export default defineComponent({

    components: {
        MultiSelect,
    },



});
</script>
<style>
.p-multiselect-header {
    display: none;
}


</style>
