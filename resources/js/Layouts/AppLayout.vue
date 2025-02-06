<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-void-black relative overflow-hidden">
            <!-- Animated background pattern -->
            <div class="absolute inset-0 opacity-10 z-0">
                <div class="spooky-pattern w-full h-full"></div>
            </div>

            <nav class="bg-black/70 backdrop-blur-md border-b border-blood-red relative z-10">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                <span
                                    class="text-2xl font-creepster text-blood-red animate__animated animate__rubberBand">
                                    Knull Void
                                </span>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                    class="font-im-fell text-ghost-white hover:text-blood-red transition-colors">
                                    Lair
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="ms-3 relative">
                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-blood-red text-sm leading-4 font-im-fell rounded-md text-ghost-white bg-black/30 hover:bg-blood-red/20 transition-colors">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ms-2 -me-0.5 size-4 text-blood-red"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60 bg-black/90 border border-blood-red/50 backdrop-blur-sm">
                                            <!-- Team Management -->
                                            <div class="block px-4 py-2 text-xs text-blood-red font-creepster">
                                                Coven Management
                                            </div>

                                            <!-- Team Settings -->
                                            <DropdownLink
                                                :href="route('teams.show', $page.props.auth.user.current_team)"
                                                class="hover:bg-blood-red/10 text-ghost-white font-im-fell">
                                                Coven Rituals
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                :href="route('teams.create')"
                                                class="hover:bg-blood-red/10 text-ghost-white font-im-fell">
                                                Summon New Coven
                                            </DropdownLink>

                                            <!-- Team Switcher -->
                                            <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                <div class="border-t border-blood-red/30" />

                                                <div class="block px-4 py-2 text-xs text-blood-red font-creepster">
                                                    Switch Covens
                                                </div>

                                                <template v-for="team in $page.props.auth.user.all_teams"
                                                    :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button" class="hover:bg-blood-red/10">
                                                            <div
                                                                class="flex items-center font-im-fell text-ghost-white">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                                    class="me-2 size-5 text-blood-red"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-blood-red rounded-full focus:outline-none focus:border-blood-red/50 transition">
                                            <img class="size-8 rounded-full object-cover"
                                                :src="$page.props.auth.user.profile_photo_url"
                                                :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-blood-red text-sm leading-4 font-im-fell rounded-md text-ghost-white bg-black/30 hover:bg-blood-red/20 transition-colors">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 size-4 text-blood-red"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="bg-black/90 border border-blood-red/50 backdrop-blur-sm">
                                            <!-- Account Management -->
                                            <div class="block px-4 py-2 text-xs text-blood-red font-creepster">
                                                Soul Management
                                            </div>

                                            <DropdownLink :href="route('profile.show')"
                                                class="hover:bg-blood-red/10 text-ghost-white font-im-fell">
                                                Profile
                                            </DropdownLink>

                                            <!-- <DropdownLink 
                                                v-if="$page.props.jetstream.hasApiFeatures" 
                                                :href="route('api-tokens.index')"
                                                class="hover:bg-blood-red/10 text-ghost-white font-im-fell"
                                            >
                                                Shadow Tokens
                                            </DropdownLink> -->

                                            <div class="border-t border-blood-red/30" />

                                            <!-- Authentication -->
                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button"
                                                    class="hover:bg-blood-red/10 text-ghost-white font-im-fell">
                                                    Logout
                                                </DropdownLink>
                                            </form>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-blood-red hover:bg-blood-red/20 transition-colors">
                                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                    class="sm:hidden bg-black/90 backdrop-blur-md border-b border-blood-red">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                            class="font-im-fell text-ghost-white hover:bg-blood-red/20">
                            Necropolis
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-blood-red/30">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                <img class="size-10 rounded-full object-cover border border-blood-red"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div>
                                <div class="font-im-fell text-base text-ghost-white">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-im-fell text-sm text-blood-red">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')"
                                class="text-ghost-white hover:bg-blood-red/20 font-im-fell">
                                Mortal Vessel
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures"
                                :href="route('api-tokens.index')" :active="route().current('api-tokens.index')"
                                class="text-ghost-white hover:bg-blood-red/20 font-im-fell">
                                Shadow Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button"
                                    class="text-ghost-white hover:bg-blood-red/20 font-im-fell">
                                    Return to Dust
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-blood-red/30" />

                                <div class="block px-4 py-2 text-xs text-blood-red font-creepster">
                                    Coven Management
                                </div>

                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)"
                                    :active="route().current('teams.show')"
                                    class="text-ghost-white hover:bg-blood-red/20 font-im-fell">
                                    Coven Rituals
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams"
                                    :href="route('teams.create')" :active="route().current('teams.create')"
                                    class="text-ghost-white hover:bg-blood-red/20 font-im-fell">
                                    Summon New Coven
                                </ResponsiveNavLink>

                                <!-- Team Switcher -->
                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                    <div class="border-t border-blood-red/30" />

                                    <div class="block px-4 py-2 text-xs text-blood-red font-creepster">
                                        Switch Covens
                                    </div>

                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <ResponsiveNavLink as="button"
                                                class="text-ghost-white hover:bg-blood-red/20 font-im-fell">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                        class="me-2 size-5 text-blood-red"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </ResponsiveNavLink>
                                        </form>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-black/50 backdrop-blur-sm border-b border-blood-red shadow-void">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="relative py-12">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.bg-void-black {
    background-color: #0a0a0a;
}

.border-blood-red {
    border-color: #4a0000;
}

.text-blood-red {
    color: #ff4444;
}

.text-ghost-white {
    color: #f0f0f0;
}

.font-creepster {
    font-family: 'Creepster', cursive;
}

.font-im-fell {
    font-family: 'IM Fell English SC', serif;
}

.spooky-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='52' height='26' viewBox='0 0 52 26' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234a0000' fill-opacity='0.4'%3E%3Cpath d='M10 10c0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6h2c0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4v2c-3.314 0-6-2.686-6-6 0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6zm25.464-1.95l8.486 8.486-1.414 1.414-8.486-8.486 1.414-1.414z' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.shadow-void {
    box-shadow: 0 4px 24px rgba(74, 0, 0, 0.3);
}
</style>