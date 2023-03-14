<template>
    <JetFormSection @submitted="updatePassword">
        <template #title>
            Update Organisation Details
        </template>

        <template #description>
            You can update your organisation details here.
        </template>

        <template #form v-if="organisation">

            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="file" value="Organisation Name"/>
                <span>{{ organisation.name }}</span>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="file" value="Organisation Phone"/>
                <span>{{ organisation.phone_number }}</span>
            </div>

            <div class="col-span-2 sm:col-span-4">
                <JetLabel for="file" value="Organisation Logo"/>
                <div class="flex flex-row justify-evenly">
                    <div class="">
                        <img alt="" :src="'/organisation/logo?' + random" @load="logoLoaded" v-if="organisation.hasLogo">
                        <JetApplicationMark class="bg-brand-500" v-else/>
                    </div>
                    <div class="content-center flex items-center">
                        <DeleteOrganisationLogo v-if="organisation.hasLogo">
                            <jet-button-small class="bg-red-500 rounded hover:bg-red-800 cursor-pointer pt-3 pb-2 px-5"
                                              dusk="delete-logo-button"
                            ><i class="fa fa-trash text-white text-2xl"></i>
                            </jet-button-small>
                        </DeleteOrganisationLogo>
                    </div>
                </div>
            </div>
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="org_color" value="Email Header Color"/>
                <div :style="color" class="text-white text-center"
                     v-if="color">
                    #{{ color }}
                </div>
                <div v-else>
                    No color selected.
                </div>
            </div>
        </template>

        <template #actions>
            <div>
                <Organisation :organisation="organisation">
                    <JetButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                               dusk="edit_organisation" id="edit_organisation">
                        Edit
                    </JetButton>
                </Organisation>
            </div>
        </template>
    </JetFormSection>
</template>
<script>
import {ref, toRefs, computed, onMounted, reactive, defineComponent} from 'vue';
import {useForm, usePage} from '@inertiajs/inertia-vue3';
import JetButton from '@/Components/Button.vue';
import JetFormSection from '@/Components/FormSection.vue';
import JetLabel from '@/Components/Label.vue';
import Organisation from "./Organisation.vue";
import DeleteOrganisationLogo from "./DeleteOrganisationLogo.vue";
import JetApplicationMark from '@/Components/ApplicationMark.vue';
import AdminDashboard from "../../Dashboards/AdminDashboard";
import PlatformAdminDashboard from "../../Dashboards/PlatformAdminDashboard";
import SuperAdminDashboard from "../../Dashboards/SuperAdminDashboard";
import AgentDashboard from "../../Dashboards/AgentDashboard";

export default defineComponent({
    components: {
        JetButton,
        JetFormSection,
        JetLabel,
        Organisation,
        DeleteOrganisationLogo,
        JetApplicationMark,
        AdminDashboard,
        PlatformAdminDashboard,
        SuperAdminDashboard,
        AgentDashboard,
    },
    setup(props) {
        const organisation = ref(null);
        const color = ref(null);
        const random = Math.random();
        const form = useForm({});

        onMounted(() => {
            axios.get(route('organisation.show'))
                .then((response) => {
                    organisation.value = response.data;
                    color.value = 'background-color:#' + organisation.value.header_mail_color;
                })
        })

        return {
            color,
            form,
            random,
            organisation,
        }
    }
})


</script>
