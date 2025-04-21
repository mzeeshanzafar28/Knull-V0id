
<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { debounce } from 'lodash-es';

const props = defineProps({
    chatrooms: Object,
    filters: Object
});

const form = useForm({});
const search = ref(props.filters?.search || '');
const isLoading = ref(false);

import { playSound } from '@/utils/sounds';
playSound('god_user_chatrooms');

// Real-time search with debounce
watch(search, debounce((value) => {
    isLoading.value = true;
    router.get(route('hell.chatrooms'), 
        { search: value },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => isLoading.value = false
        }
    );
}, 300));

const deleteChamber = (id) => {
    if (confirm('Eradicate this chamber forever?')) {
        form.delete(route('chatrooms.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Void Chambers" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="horror-font text-blood text-4xl glitch-text" data-text="VOID CHAMBERS">
                VOID CHAMBERS
            </h2>
        </template>

        <div class="py-6 px-4">
            <div class="mb-6 flex justify-between items-center">
                <div class="w-full max-w-md relative">
            <input 
                type="text" 
                v-model="search"
                placeholder="Search the void..." 
                class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood pr-10"
            />
            <div v-if="isLoading" class="absolute right-3 top-3.5">
                <div class="animate-spin text-blood">⛧</div>
            </div>
        </div>
                <Link 
                    :href="route('chatrooms.create')"
                    class="horror-font bg-blood/20 text-blood px-6 py-3 rounded-lg border-2 border-blood hover:bg-blood/40 hover:text-white transition-all duration-300 flex items-center gap-2"
                >
                    <span class="text-xl">⛧</span>
                    FORGE CHAMBER
                </Link>
            </div>

            <div class="bg-void-black rounded-lg border-2 border-blood overflow-hidden">
                <table class="w-full">
                    <thead class="bg-blood">
                        <tr>
                            <th class="horror-font text-blood p-4 text-left">Name</th>
                            <th class="horror-font text-blood p-4 text-left">Description</th>
                            <th class="horror-font text-blood p-4">Soul Capacity</th>
                            <th class="horror-font text-blood p-4">Ephemeral</th>
                            <th class="horror-font text-blood p-4">Destruction Time</th>
                            <th class="horror-font text-blood p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="chatroom in chatrooms.data" 
                            :key="chatroom.id"
                            class="hover:bg-blood/5 transition-colors duration-300 group"
                        >
            <td class="p-4 border-t-2 border-blood/20">
                <span class="horror-font text-blood glitch-text" :data-text="chatroom.name">
                    {{ chatroom.name }}
                </span>
            </td>
            <td class="p-4 border-t-2 border-blood/20 horror-font text-blood">
                {{ chatroom.description || 'Silent whispers...' }}
            </td>
            <td class="p-4 border-t-2 border-blood/20 text-center horror-font text-blood">
                {{ chatroom.max_members ?? '∞' }}
            </td>
            <td class="p-4 border-t-2 border-blood/20 text-center">
                <span class="text-2xl" v-if="chatroom.is_ephemeral">💀</span>
                <span v-else class="horror-font text-blood/50">–</span>
            </td>
            <td class="p-4 border-t-2 border-blood/20 text-center horror-font text-blood">
                <template v-if="chatroom.is_ephemeral">
                    <div class="flex flex-col items-center">
                        <span class="text-sm text-blood/60">
                            {{ chatroom.self_destruct_hours }}h lifespan
                        </span>
                    </div>
                </template>
                <span v-else>Eternal damnation</span>
            </td>
            <td class="p-4 border-t-2 border-blood/20 text-right">
            <div class="flex gap-3 justify-end">
                <Link 
                    :href="route('chatrooms.edit', chatroom.id)"
                    class="text-blood hover:text-white transition-colors duration-300"
                >
                    <span class="text-xl">✎</span>
                </Link>
                <button 
                    @click="deleteChamber(chatroom.id)"
                    class="text-blood hover:text-white transition-colors duration-300"
                >
                    <span class="text-xl">🗡</span>
                </button>
            </div>
        </td>
        </tr>
    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center" v-if="chatrooms.links.length > 3">
                <div class="flex gap-2">
                    <Link 
                        v-for="(link, index) in chatrooms.links"
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
th {
    border-bottom: 2px solid rgba(128, 0, 0, 0.3);
}

tr:hover td {
    background: linear-gradient(90deg, #ff000005 0%, #00000000 100%);
}

.glow {
    text-shadow: 0 0 10px #ff000080;
}
</style>