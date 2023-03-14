<template>
    <div class="p-3">
        <div v-for="searchItem in advancedSearch">
            <div class="grid grid-cols-2 gap-2 mb-2" v-if="searchItem.type === 'date'">
                <jet-label class="mt-2 text-gray-600">{{ searchItem.label }}</jet-label>
                <div class="relative w-full">
                    <jet-date-picker name="searchItem.name" type="date"
                                     v-model="searchItem.model"
                                     class="text-sm mt-3 block w-full pt-1 pb-1.5 pr-3 pl-10"
                                     autofocus
                                     pattern='(?:((?:0[1-9]|1[0-9]|2[0-9])\/(?:0[1-9]|1[0-2])|(?:30)\/(?!02)(?:0[1-9]|1[0-2])|31\/(?:0[13578]|1[02]))\/(?:19|20)[0-9]{2})'/>
                    <div class="absolute left-3 top-2">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-2" v-if="searchItem.type === 'select'">
                <jet-label class="mt-2 text-gray-600">{{ searchItem.label }}</jet-label>
                <jet-drop-down-widget class="w-full" v-model="searchItem.model" v-bind:items="searchItem.options"
                                      name="searchItem.name"/>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-2" v-if="searchItem.type === 'input'">
                <jet-label class="mt-2 text-gray-600">{{ searchItem.label }}</jet-label>
                <jet-input class="w-full" type="text" v-model="searchItem.model" name="searchItem.name"/>
            </div>
            <div class="mb-2 mt-2 mb-2" v-if="searchItem.type === 'checkbox'">
                <jet-checkbox type="checkbox" v-model="searchItem.model" name="searchItem.name"/>
                <span class="ml-2 text-sm text-gray-600">{{ searchItem.label }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import {defineComponent} from "vue";
import JetLabel from "@/Components/Label.vue";
import JetDatePicker from "@/Components/DatePicker";
import JetDropDownWidget from "@/Components/DropDownWidget";
import JetInput from "@/Components/Input.vue";
import JetCheckbox from "@/Components/Checkbox.vue";


export default defineComponent({
    props: {
        advancedSearch: Array,
    },
    components: {
        JetLabel,
        JetDatePicker,
        JetDropDownWidget,
        JetInput,
        JetCheckbox,
    },
    methods: {
        showModal() {
            this.show = true;
        },
        closeModal() {
            this.show = false;
        },
    },
    data() {
        return {
            selected: "",
            show: false,
            tableData: null,
        }
    },
    dataService: null,
})
</script>

<style scoped>

</style>
