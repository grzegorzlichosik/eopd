<template>
    <div class="flex flex-row mb-3">
        <div class="basis-1/3 font-bold">Objective</div>
        <div class="basis-2/3">
            {{ flow.objective }}
        </div>
        <div class="basis-1/3">

        </div>

    </div>
    <div class="flex flex-row mb-3">
        <div class="basis-1/3 font-bold">Status</div>
        <div class="basis-2/3">
            <span
                class="text-gray-600 font-semibold px-1.5 py-1 rounded bg-green-300 w-fit">Published</span>
        </div>
        <div class="basis-1/3">
        </div>
    </div>
    <div class="flex flex-row mb-3">
        <div class="basis-1/3 font-bold">Channels</div>
        <div class="basis-1/3 text-sm"></div>
        <div class="basis-2/3 text-sm"></div>
    </div>

    <div class="flex flex-row mb-3">
        <div class="basis-1/3 font-bold"></div>
        <div class="basis-2/3">
            <table class="mt-4 table-auto w-full">
                <tr class="text-left">
                    <th class="pb-2">Type</th>
                    <th class="pb-2">Max. Participants</th>
                    <th class="pb-2">Default</th>
                    <th class="pb-2">Auto Confirm</th>
                </tr>
                <tr v-for="(item, index) of channelsToRender" :key="index" class="pb-2">
                    <td class="pb-2">
                        <div
                            class="text-gray-600 text-xs font-semibold px-1.5 py-1 rounded bg-green-300 w-fit"
                            :class="{'bg-green-300': item.max_participants, 'bg-gray-300': !item.max_participants }"
                        >
                            {{ item.name }}
                        </div>
                    </td>
                    <td class="pb-2">
                        {{ item.max_participants ? item.max_participants : 0 }}
                    </td>
                    <td class="pb-2">
                        {{ item.is_default ? 'Yes' : 'No' }}
                    </td>
                    <td class="pb-2">
                        {{ item.is_auto_confirm ? 'Yes' : 'No' }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="basis-1/3"></div>
    </div>
</template>
<script>
import {defineComponent, onMounted, reactive, watch} from "vue";
import Header1 from "@/Components/Header1";
import Header2 from "@/Components/Header2";
import Header3 from "@/Components/Header3";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetLabel from "@/Components/Label.vue";

export default defineComponent({
    props: {
        flow: Object,
        channelTypes: Array,
    },

    components: {
        Header1,
        Header2,
        Header3,
        AppLayout,
        JetLabel,
    },
    setup(props) {

        const channelsToRender = reactive([]);

        onMounted(() => {
            props.channelTypes.forEach((type, index) => {
                channelsToRender.push({});
                let temp = props.flow.channels.find(value => value.type.uuid === type.uuid) || {};
                temp['name'] = type.name;
                temp['channelTypeUuid'] = type.uuid;
                channelsToRender[index] = temp
            })
        })

        watch(() => props.flow.channels, flow => {
            channelsToRender.value = [];
            props.channelTypes.forEach((type, index) => {
                let temp = props.flow.channels.find(value => value.type.uuid === type.uuid) || {};
                temp['name'] = type.name;
                temp['channelTypeUuid'] = type.uuid;
                channelsToRender[index] = temp
            })

        })

        return {
            channelsToRender
        }
    }
});
</script>
