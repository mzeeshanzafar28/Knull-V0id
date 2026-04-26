<script setup>
import { ref, watch } from 'vue';
import { router, usePage, Head, Link } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { playSound } from '@/utils/sounds';
playSound('users');

const props = defineProps({
    users: Object,
});

const currentUser = usePage().props.auth.user;
const search = ref('');

const deleteUser = (id) => {
    if (id === currentUser.id) return;
    if (confirm('Are you sure you want to banish this soul to the void?')) {
        router.delete(route('users.destroy', id));
    }
};

watch(search, debounce((value) => {
    router.get(route('hell.users'), { search: value }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300));
</script>

<template>
    <Head title="Damned Souls" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="horror-font text-blood text-4xl glitch-text" data-text="SOUL VATICUM">
                SOUL VATICUM
            </h2>
        </template>

        <div class="py-6 px-4 bg-void-dark min-h-screen">
            <div class="flex justify-between items-center mb-6">
                <div class="relative w-full max-w-md">
                    <input 
                        v-model="search"
                        type="text" 
                        class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood placeholder-blood/50"
                        placeholder="Scry the damned..."
                    >
                    <span class="absolute right-3 top-3 text-blood">🔮</span>
                </div>
                <Link 
                    :href="route('users.create')"
                    class="horror-font bg-blood/20 text-blood px-6 py-3 rounded-lg border-2 border-blood hover:bg-blood/40 hover:text-white transition-all duration-300 flex items-center gap-2"
                >
                    <span class="text-xl">⛧</span>
                    CONJURE SOUL
                </Link>
            </div>

            <div class="bg-void-black rounded-lg border-2 border-blood overflow-hidden shadow-void">
                <table class="w-full">
                    <thead class="bg-blood">
                        <tr>
                            <th class="horror-font text-blood p-4 text-left">Name</th>
                            <th class="horror-font text-blood p-4 text-left">Soul Sigil</th>
                            <th class="horror-font text-blood p-4">Status</th>
                            <th class="horror-font text-blood p-4 text-right">Rites</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="user in users.data" 
                            :key="user.id"
                            class="hover:bg-blood/5 transition-colors duration-300 group border-b border-blood/10"
                        >
                            <td class="p-4">
                                <span class="horror-font text-blood glitch-text" :data-text="user.name">
                                    {{ user.name }}
                                    <span v-if="user.id === currentUser.id" class="text-blood/50">(YOU)</span>
                                </span>
                            </td>
                            <td class="p-4 text-blood font-mono">{{ user.email }}</td>
                            <td class="p-4 text-center">
                                <span class="horror-font text-blood animate-pulse" v-if="user.is_god_user">DARK COVENANT</span>
                                <span class="text-blood" v-else>LOST SOUL</span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex gap-3 justify-end items-center">
                                    <Link 
                                        v-if="user.id !== currentUser.id"
                                        :href="route('users.edit', user.id)"
                                        class="text-blood hover:text-white transition-colors duration-300"
                                    >
                                        ✎
                                    </Link>
                                    <Link 
                                        :href="route('investigate.user', user.id)"
                                        class="text-blood hover:text-white transition-colors duration-300"
                                    >
                                        🔍
                                    </Link>
                                    <button 
                                        v-if="user.id !== currentUser.id"
                                        @click="deleteUser(user.id)"
                                        class="text-blood hover:text-white transition-colors duration-300"
                                    >
                                        🗡
                                    </button>
                                    <span v-if="user.id === currentUser.id" class="text-blood/50">—</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center" v-if="users.links.length > 3">
                <div class="flex gap-2">
                    <Link 
                        v-for="(link, index) in users.links"
                        :key="index"
                        :href="link.url || '#'"
                        :class="[
                            'horror-font px-4 py-2 rounded-lg border-2',
                            link.active 
                                ? 'border-blood bg-blood/20 text-white' 
                                : 'border-blood/30 text-blood/50 hover:border-blood'
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.shadow-void {
    box-shadow: 0 0 20px rgba(128, 0, 0, 0.3);
}

.glow {
    text-shadow: 0 0 10px #ff000080;
}

tr:hover td {
    background: linear-gradient(90deg, #ff000005 0%, #00000000 100%);
}
</style>