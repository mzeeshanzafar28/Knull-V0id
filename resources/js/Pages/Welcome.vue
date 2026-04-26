<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useIntervalFn } from '@vueuse/core';
import { ref } from 'vue';
import { playSound } from '@/utils/sounds';


defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const flickerText = ref(false);
const shadowX = ref(0);
const shadowY = ref(0);

// Create random flicker effect
useIntervalFn(() => {
    flickerText.value = !flickerText.value;
    setTimeout(() => flickerText.value = !flickerText.value, 50);
}, 3000);

// Animated shadow movement
useIntervalFn(() => {
    shadowX.value = Math.random() * 20 - 10;
    shadowY.value = Math.random() * 20 - 10;
}, 5000);

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
}
</script>

<template>

    <Head title="Welcome" />
    <div class="min-h-screen relative overflow-hidden">
        <!-- Top Right Button -->
        <header class="absolute top-0 right-0 z-50 p-6 text-red-600">
            <nav v-if="canLogin" class="flex justify-end">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                    class="rounded-md px-3 py-2 font-im-fell text-ghost-white ring-1 ring-blood-red transition hover:bg-gray-900 hover:ring-blood-red/50 focus:outline-none">
                Enter the Lair
                </Link>

                <template v-else>
                    <Link :href="route('login')"
                        class="rounded-md px-3 py-2 font-im-fell text-ghost-white ring-1 ring-blood-red transition hover:bg-gray-900 hover:ring-blood-red/50 focus:outline-none">
                    Summon Portal
                    </Link>

                    <Link v-if="canRegister" :href="route('register')"
                        class="ml-3 rounded-md px-3 py-2 font-im-fell text-blood-red bg-blood-red/10 ring-1 ring-blood-red transition hover:bg-gray-900 hover:ring-blood-red/50 focus:outline-none">
                    Bind Soul
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Centered Main Content -->
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-blood-red selection:text-ghost-white">
            <div class="relative w-full max-w-4xl px-6 lg:max-w-7xl z-10">
                <!-- Centered CTA Button -->
                <main class="flex-grow flex items-center justify-center min-h-[80vh]">
                    <div class="text-center py-12">
                        <div class="relative inline-block">
                            <div class="absolute inset-0 bg-blood-red/30 blur-3xl -z-10 animate-pulse" :style="{
                                transform: `translate(${shadowX}px, ${shadowY}px)`
                            }"></div>
                            <Link href="/register" @mouseenter="playSound('terror')"
                                class="text-white transition-all hover:bg-red-600 hover:border-red-900 px-12 py-4 text-2xl font-creepster bg-blood-red/10 border-2 border-blood-red rounded-lg hover:bg-blood-red/20 transition-colors animate__animated animate__pulse animate__infinite">
                            Begin Descent into the Void
                            </Link>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<style>
body {
    background-image: url('/images/welcome-bg.png');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
    position: relative;
    overflow: hidden;
}

/* Glitch effect container */
body::before,
body::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: inherit;
    background-size: cover;
    background-position: center;
    opacity: 0;
    z-index: -1;
    animation: glitch-anim 2s infinite;
}

body::before {
    animation-delay: 1s;
    clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%);
}

body::after {
    animation-delay: 1.5s;
    clip-path: polygon(0 55%, 100% 55%, 100% 100%, 0 100%);
}

@keyframes glitch-anim {
    0% {
        opacity: 0;
        transform: translate(0);
    }

    2% {
        opacity: 1;
        transform: translate(-2px, 2px);
    }

    4% {
        transform: translate(2px, -2px);
    }

    6% {
        transform: translate(-2px, 2px);
    }

    8% {
        transform: translate(2px, -2px);
    }

    10% {
        transform: translate(-2px, 2px);
    }

    12% {
        opacity: 1;
        transform: translate(0);
    }

    100% {
        opacity: 0;
        transform: translate(0);
    }
}

/* Add a subtle scanline effect */
body::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(rgba(0, 0, 0, 0) 50%,
            rgba(0, 0, 0, 0.1) 50%);
    background-size: 100% 4px;
    animation: scanline 6s linear infinite;
    pointer-events: none;
}

@keyframes scanline {
    from {
        transform: translateY(-100%);
    }

    to {
        transform: translateY(100%);
    }
}

/* Existing animations and other styles */
@keyframes float {
    0% {
        transform: translateY(0) rotate(0deg);
    }

    50% {
        transform: translateY(-20px) rotate(180deg);
    }

    100% {
        transform: translateY(0) rotate(360deg);
    }
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 30px rgba(255, 68, 68, 0.3);
}

.drop-shadow-void {
    text-shadow: 0 0 10px rgba(255, 68, 68, 0.5),
        0 0 20px rgba(255, 68, 68, 0.3),
        0 0 30px rgba(255, 68, 68, 0.2);
}

.text-flicker {
    animation: flicker 0.15s infinite;
}

@keyframes flicker {
    0% {
        opacity: 1;
    }

    50% {
        opacity: 0.8;
    }

    100% {
        opacity: 1;
    }
}
</style>
