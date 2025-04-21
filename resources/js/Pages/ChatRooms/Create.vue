<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    name: '',
    description: '',
    max_members: null,
    is_ephemeral: false,
    self_destruct_hours: null
});
</script>

<template>
    <Head title="Forge Chamber" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="horror-font text-blood text-4xl glitch-text" data-text="FORGE CHAMBER">
                FORGE CHAMBER
            </h2>
        </template>

        <div class="py-6 px-4">
            <div class="max-w-2xl mx-auto bg-void-black p-8 rounded-lg border-2 border-blood relative overflow-hidden">
                <div class="absolute inset-0 bg-blood/5 animate-pulse -z-10"></div>
                
                <form @submit.prevent="form.post(route('chatrooms.store'))" class="space-y-6">
                    <div>
                        <label class="horror-font text-blood block mb-2">Chamber Name</label>
                        <input v-model="form.name" type="text" required
                            class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood"
                            placeholder="Scream into the void..."
                        >
                        <p v-if="form.errors.name" class="text-red-600 horror-font mt-2">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="horror-font text-blood block mb-2">Description</label>
                        <textarea v-model="form.description"
                            class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood h-32"
                            placeholder="Whispers of torment..."
                        ></textarea>
                    </div>

                    <div>
                        <label class="horror-font text-blood block mb-2">Soul Capacity</label>
                        <input v-model="form.max_members" type="number"
                            class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood"
                            placeholder="∞ (Leave empty for endless torment)"
                        >
                    </div>

                    <div class="flex items-center gap-4">
                        <input v-model="form.is_ephemeral" type="checkbox" id="ephemeral"
                            class="w-5 h-5 text-blood bg-void-black border-2 border-blood rounded focus:ring-blood">
                        <label for="ephemeral" class="horror-font text-blood">Ephemeral Chamber</label>
                    </div>

                    <div v-if="form.is_ephemeral">
                        <label class="horror-font text-blood block mb-2">Destruction Time (Hours)</label>
                        <input v-model="form.self_destruct_hours" type="number" required
                            class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood"
                            placeholder="Hours until oblivion..."
                        >
                        <p v-if="form.errors.self_destruct_hours" class="text-red-600 horror-font mt-2">
                            {{ form.errors.self_destruct_hours }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-4">
                        <Link :href="route('hell.chatrooms')" 
                            class="horror-font px-6 py-3 text-blood hover:text-white transition-colors duration-300">
                            Cancel
                        </Link>
                        <button type="submit" 
                            class="horror-font bg-blood/20 text-blood px-6 py-3 rounded-lg border-2 border-blood hover:bg-blood/40 hover:text-white transition-all duration-300 flex items-center gap-2"
                            :disabled="form.processing">
                            <span class="text-xl">⛧</span>
                            Forge Chamber
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>