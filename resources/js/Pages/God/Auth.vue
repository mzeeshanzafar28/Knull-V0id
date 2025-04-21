<script setup>
import { useForm, Head, usePage } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import { playSound } from '@/utils/sounds';

const page = usePage();
const form = useForm({ hell_pass: '' });

// Reactive banned state
const isBanned = ref(page.props.isBanned);

// Watch for error changes
watchEffect(() => {
    const error = form.errors.hell_pass || page.props.errors?.hell_pass;
    isBanned.value = error?.toLowerCase().includes('banished') || 
                    error?.toLowerCase().includes('void');
});

const submit = () => {
    if (isBanned.value) return;
    
    form.post(route('god.board.authenticate'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

playSound('drums_of_doom');

</script>

<template>
    <Head title="Hell Pass" />
    <div class="min-h-screen bg-void-dark flex items-center justify-center relative overflow-hidden">
        <div class="blood-drip"></div>
        <div class="bg-void-black p-8 rounded-lg shadow-void w-96 z-10 transform hover:skew-x-2 transition-all duration-500">
            <h1 class="horror-font text-blood text-3xl mb-6 text-center glitch-text" data-text="HELL GATE">
                HELL GATE
            </h1>
            <div v-if="form.errors.hell_pass || page.props.errors?.hell_pass" 
                 class="text-red-600 text-center mb-4 horror-font">
                {{ form.errors.hell_pass || page.props.errors?.hell_pass }}
            </div>
            <form @submit.prevent="submit" class="space-y-6">
                <div class="relative">
                    <input v-model="form.hell_pass" 
                        type="text" 
                        class="horror-font bg-transparent w-full p-3 text-blood border-2 border-blood rounded-lg focus:outline-none focus:border-800000 focus:ring-2 focus:ring-blood placeholder-blood/50"
                        placeholder="Speak the forbidden words..."
                        :disabled="form.processing || isBanned">
                    <div class="absolute inset-y-0 right-3 flex items-center">
                        <span class="text-blood">🔑</span>
                    </div>
                </div>
                <button type="submit" 
                    class="horror-font w-full bg-blood/20 text-blood py-3 rounded-lg border-2 border-blood hover:bg-blood/40 hover:text-white transition-all duration-300 flex items-center justify-center gap-2"
                    :disabled="form.processing || isBanned">
                    <span class="animate-pulse">⛧</span> 
                    ENTER THE VOID 
                    <span class="animate-pulse">⛧</span>
                </button>
            </form>
        </div>
    </div>
</template>

<style>
.blood-drip {
  position: absolute;
  width: 200%;
  height: 200%;
  background: 
    radial-gradient(circle at 20% 10%, #ff000040 5%, transparent 5%),
    radial-gradient(circle at 80% 90%, #ff000040 5%, transparent 5%);
  animation: drip 10s infinite linear;
}

@keyframes drip {
  0% { transform: translateY(-100%); }
  100% { transform: translateY(100%); }
}
</style>