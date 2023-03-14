<template>
    <div>
        <Head :title="title"/>
        <JetBanner/>
        <div class="min-h-screen bg-gray-100">
            <nav
                :class="{'bg-brand2-900': $page.props.user?.organisation.is_platform}"
                class="bg-brand-500 border-b border-gray-100"
                ref="nav"
            >
                <!-- Primary Navigation Menu -->
                <div class="container mx-auto px-4">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <JetApplicationMark class="block h-24 w-auto -ml-8"/>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <JetNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </JetNavLink>
                                <JetNavLink v-if="$page.props.user.is_super_admin || $page.props.user.is_admin"
                                            :href="route('admin.users')" :active="route().current('admin.users')">
                                    Users
                                </JetNavLink>
                                <JetNavLink
                                    v-if="!$page.props.user?.organisation.is_platform && ($page.props.user.is_super_admin || $page.props.user.is_admin)"
                                    :href="route('admin.pools')" :active="route().current('admin.pools')">
                                    Pools
                                </JetNavLink>
                                <JetNavLink
                                    v-if="!$page.props.user?.organisation.is_platform && ($page.props.user.is_super_admin || $page.props.user.is_admin)"
                                    :href="route('admin.resources.locations.index')"
                                    :active="route().current('admin.resources.locations.index')">
                                    Resources
                                </JetNavLink>
                                <JetNavLink
                                    v-if="!$page.props.user?.organisation.is_platform && ($page.props.user.is_super_admin || $page.props.user.is_admin)"
                                    :href="route('admin.flows.index')" :active="route().current('admin.flows.index')">
                                    Flows
                                </JetNavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <!-- Teams Dropdown -->
                                <JetDropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                {{ $page.props.user.current_team.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path fill-rule="evenodd"
                                                          d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <JetDropdownLink
                                                    :href="route('teams.show', $page.props.user.current_team)">
                                                    Team Settings
                                                </JetDropdownLink>

                                                <JetDropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                                 :href="route('teams.create')">
                                                    Create New Team
                                                </JetDropdownLink>

                                                <div class="border-t border-gray-100"/>

                                                <!-- Team Switcher -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <JetDropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg
                                                                    v-if="team.id == $page.props.user.current_team_id"
                                                                    class="mr-2 h-5 w-5 text-green-400"
                                                                    fill="none"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    stroke="currentColor"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <path
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </JetDropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </JetDropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <JetDropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                 :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ $page.props.user.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path fill-rule="evenodd"
                                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <JetDropdownLink :href="route('profile.show')">
                                            Profile
                                        </JetDropdownLink>

                                        <div class="border-t border-gray-100"/>

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <JetDropdownLink as="button">
                                                Log Out
                                            </JetDropdownLink>
                                        </form>
                                    </template>
                                </JetDropdown>
                            </div>
                            <!-- Authentication -->
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
                                @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}"
                     class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <JetResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </JetResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover"
                                     :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <JetResponsiveNavLink :href="route('profile.show')"
                                                  :active="route().current('profile.show')">
                                Profile
                            </JetResponsiveNavLink>

                            <JetResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures"
                                                  :href="route('api-tokens.index')"
                                                  :active="route().current('api-tokens.index')">
                                API Tokens
                            </JetResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <JetResponsiveNavLink as="button">
                                    Log Out
                                </JetResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200"/>

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <JetResponsiveNavLink :href="route('teams.show', $page.props.user.current_team)"
                                                      :active="route().current('teams.show')">
                                    Team Settings
                                </JetResponsiveNavLink>

                                <JetResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams"
                                                      :href="route('teams.create')"
                                                      :active="route().current('teams.create')">
                                    Create New Team
                                </JetResponsiveNavLink>

                                <div class="border-t border-gray-200"/>

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <JetResponsiveNavLink as="button">
                                            <div class="flex items-center">
                                                <svg
                                                    v-if="team.id == $page.props.user.current_team_id"
                                                    class="mr-2 h-5 w-5 text-green-400"
                                                    fill="none"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </JetResponsiveNavLink>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow" ref="header">
                <div class="container mx-auto py-6 px-4">
                    <slot name="header"/>
                </div>
            </header>

            <!-- Page Content -->
            <main class="container mx-auto"
                  :style="{ minHeight: bodyMinHeight, marginBottom: 0 }">
                <slot/>
            </main>
            <footer>
                <JetFooter ref="footer"/>
            </footer>
            <jet-dialog-modal :show="$page.props.flash.message" @close="closeFlashModal">
                <template #content>
                    {{ $page.props.flash.message }}
                </template>
                <template #footer>
                    <jet-button dusk="flash-close" class="ml-4 justify-center bg-brand-900 text-white "
                                @click="closeFlashModal">Close
                    </jet-button>

                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>
<script>
import {defineComponent, ref, nextTick, onMounted, watch} from 'vue';
import {Inertia} from '@inertiajs/inertia';
import {Head, Link, usePage} from '@inertiajs/inertia-vue3';
import JetApplicationMark from '@/Components/ApplicationMark.vue';
import JetBanner from '@/Components/Banner.vue';
import JetDropdown from '@/Components/Dropdown.vue';
import JetDropdownLink from '@/Components/DropdownLink.vue';
import JetNavLink from '@/Components/NavLink.vue';
import JetResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import JetFooter from '@/Components/Footer.vue';
import JetDialogModal from "@/Components/DialogModal.vue";
import JetButton from "@/Components/Button.vue";
import {useToast} from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

export default defineComponent({
    props: {
        title: String,
    },

    components: {
        Head,
        JetApplicationMark,
        JetBanner,
        JetDropdown,
        JetDropdownLink,
        JetNavLink,
        JetResponsiveNavLink,
        JetDialogModal,
        JetButton,
        Link,
        JetFooter,
    },

    setup(props, context) {
        const showingNavigationDropdown = ref(false);

        const bodyMinHeight = ref('100vh');
        const header = ref(null);
        const footer = ref(null);
        const nav = ref(null);
        let timer = ref(null);
        const toast = useToast({position: "bottom-right"});

        const logout = () => {
            Inertia.post(route('logout'));
        };
        const closeFlashModal = () => {
            usePage().props.value.flash.message = null;
        }

        onMounted(() => {
            bodyMinHeight.value = `calc(100vh - (${nav.value.clientHeight}px + ${header.value.clientHeight}px + 2px + ${footer.value.$el.clientHeight}px ))`;
            timer = setInterval(() => {
                checkUser()
            }, 1800000) //check every 30 min
        })

        watch(() => usePage().props.value.flash,
            flash => {
                if (flash.toaster) {
                    toast.open({
                        message: flash.toaster.message,
                        type: flash.toaster.type ? flash.toaster.type : 'success',
                    });
                }
            }, {
                deep: true,
                immediate: true
            }
        )
        const checkUser = () => {
            axios.get('/', {validateStatus: () => true})
                .then((response) => {
                    if (response.status !== 200) {
                        window.location = "/login"
                    }
                })
                .catch((error) => {
                    window.location = "/login"
                });
        }
        const beforeDestroy = () => {
            clearInterval(timer)
        }

        return {
            showingNavigationDropdown,
            logout,
            timer,
            closeFlashModal,
            bodyMinHeight,
            header,
            footer,
            nav,
            checkUser,
            beforeDestroy,
            useToast
        }
    }
});

</script>
<style>
.p-datatable .p-datatable-tbody > tr:focus {
    outline: none;
}
</style>
