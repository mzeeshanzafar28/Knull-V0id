<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {playSound} from '@/utils/sounds';

playSound('hell_board');

const props = defineProps({
    stats: Array, 
});
</script>

<template>
    <Head title="Hell Board" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="horror-font text-blood text-4xl glitch-text" data-text="HELL BOARD">
                HELL BOARD
            </h2>
        </template>

        <div class="py-6 px-4">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div v-for="stat in stats" :key="stat.label" 
                     class="bg-void-black p-6 rounded-lg border-2 border-blood hover:border-white transition-all duration-500 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-blood animate-pulse -z-10"></div>
                    <h3 class="horror-font text-blood text-xl mb-2">
                        {{ stat.label }}
                    </h3>
                    <p class="horror-font text-4xl text-blood glitch-text" :data-text="stat.value">
                        {{ stat.value }}
                    </p>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Link :href="route('hell.users')" 
                      class="bg-void-black p-6 rounded-lg border-2 border-blood hover:border-white transform hover:scale-105 transition-all duration-500 group">
                    <div class="flex items-center gap-4">
                        <span class="text-blood text-3xl">🛐</span>
                        <div>
                            <h3 class="horror-font text-xl text-blood">SOUL FORGE</h3>
                            <p class="text-blood">Mold and shape the damned</p>
                        </div>
                    </div>
                </Link>
                
                <Link :href="route('hell.chatrooms')" 
                      class="bg-void-black p-6 rounded-lg border-2 border-blood hover:border-white transform hover:scale-105 transition-all duration-500 group">
                    <div class="flex items-center gap-4">
                        <span class="text-blood text-3xl">🔥</span>
                        <div>
                            <h3 class="horror-font text-xl text-blood">VOID CHAMBERS</h3>
                            <p class="text-blood">Carve new dimensions of torment</p>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>