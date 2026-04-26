<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    user: Object,
    messages: Array,
    total: Number
});
</script>

<template>
    <Head title="Soul Investigation" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="horror-font text-blood text-4xl glitch-text" data-text="SOUL PROBE">
                SOUL PROBE: {{ user.name }}
            </h2>
        </template>

        <div class="py-6 px-4">
            <div class="bg-void-black rounded-lg border-2 border-blood p-6">
                <div class="mb-6 horror-font text-blood text-2xl">
                    Total Screams: {{ total }}
                </div>
                
                <div class="space-y-4">
                    <div v-for="message in messages" :key="message.chat_room_id" 
                        class="border-2 border-blood/20 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="horror-font text-blood">Chamber: {{ message.room.name }}</span>
                            <span class="text-blood">Screams: {{ message.count }}</span>
                        </div>
                        <div class="relative pt-1">
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blood/10">
                                <div :style="`width: ${(message.count / total) * 100}%`" 
                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blood/50 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>