<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    user: Object
});

const form = useForm(props.user ? {
    name: props.user.name,
    email: props.user.email,
    is_god_user: props.user.is_god_user,
    password: '',
    password_confirmation: ''
} : {
    name: '',
    email: '',
    is_god_user: false,
    password: '',
    password_confirmation: ''
});

const submit = () => {
    props.user 
        ? form.put(route('users.update', props.user.id))
        : form.post(route('users.store'));
};
</script>

<template>
    <Head :title="user ? 'Edit Soul' : 'Forge Soul'" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="horror-font text-blood text-4xl glitch-text" :data-text="user ? 'TWIST SOUL' : 'FORGE SOUL'">
                {{ user ? 'TWIST SOUL' : 'FORGE SOUL' }}
            </h2>
        </template>

        <div class="py-6 px-4">
            <div class="max-w-2xl mx-auto bg-void-black p-8 rounded-lg border-2 border-blood relative overflow-hidden">
                <div class="absolute inset-0 bg-blood/5 animate-pulse -z-10"></div>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="horror-font text-blood block mb-2">Soul Name</label>
                        <input v-model="form.name" type="text" required
                            class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood">
                        <p v-if="form.errors.name" class="text-red-600 horror-font mt-2">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="horror-font text-blood block mb-2">Soul Sigil (Email)</label>
                        <input v-model="form.email" type="email" required
                            class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood">
                        <p v-if="form.errors.email" class="text-red-600 horror-font mt-2">{{ form.errors.email }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <input v-model="form.is_god_user" type="checkbox" id="god_user"
                            class="w-5 h-5 text-blood bg-void-black border-2 border-blood rounded focus:ring-blood">
                        <label for="god_user" class="horror-font text-blood">Dark Lord Status</label>
                    </div>

                    <div>
                        <label class="horror-font text-blood block mb-2">Soul Binding (Password)</label>
                        <input v-model="form.password" type="password"
                            class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood"
                            :placeholder="user ? 'Leave empty to keep existing' : ''">
                        <p v-if="form.errors.password" class="text-red-600 horror-font mt-2">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="horror-font text-blood block mb-2">Confirm Binding</label>
                        <input v-model="form.password_confirmation" type="password"
                            class="horror-font w-full bg-void-black text-blood border-2 border-blood rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blood">
                    </div>

                    <div class="flex justify-end gap-4">
                        <Link :href="route('hell.users')" 
                            class="horror-font px-6 py-3 text-blood hover:text-white transition-colors duration-300">
                            Cancel
                        </Link>
                        <button type="submit" 
                            class="horror-font bg-blood/20 text-blood px-6 py-3 rounded-lg border-2 border-blood hover:bg-blood/40 hover:text-white transition-all duration-300 flex items-center gap-2"
                            :disabled="form.processing">
                            <span class="text-xl">⛧</span>
                            {{ user ? 'Twist Soul' : 'Bind Soul' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>