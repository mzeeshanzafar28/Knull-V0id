<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    title: String,
});
</script>

<template>
    <div class="min-h-screen bg-void-dark">
        <Head :title="title" />

        <!-- Navigation -->
        <nav class="bg-void-black border-b-2 border-blood animate-pulse">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <Link :href="route('hell.board')" class="horror-font text-blood text-3xl glitch-text" data-text="Knull-V0id">
                        Knull-V0id
                    </Link>
                    <div class="flex items-center space-x-6">
                        <Link :href="route('hell.users')" class="horror-font text-blood hover:text-black hover:bg-blood px-4 py-2 rounded-lg transition-all duration-300">
                            👹 Souls
                        </Link>
                        <Link :href="route('listrooms')" class="horror-font text-blood hover:text-black hover:bg-blood px-4 py-2 rounded-lg transition-all duration-300">
                            🏚 Chambers
                        </Link>
                        <Link :href="route('logout')" method="post" class="horror-font text-blood hover:text-black hover:bg-blood px-4 py-2 rounded-lg transition-all duration-300">
                            ☠ Escape
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="bg-void-dark animate-flicker">
            <div class="blood-overlay"></div>
            <slot />
        </main>
        
        <!-- Floating particles -->
        <div class="floating-ashes"></div>
    </div>
</template>

<style>
.blood-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQIW2NkQAKrVq36zwjjgzhhYWGMYAEYB8RmROaABADeOQ8CXl/xfgAAAABJRU5ErkJggg==');
  pointer-events: none;
  opacity: 0.1;
}

.floating-ashes {
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background-image: url('data:image/svg+xml,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="1" fill="%23ff0000" opacity="0.2"/></svg>');
  animation: float 20s infinite linear;
  pointer-events: none;
}

@keyframes float {
  from { transform: translateY(-100vh); }
  to { transform: translateY(100vh); }
}
</style>