<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { playSound } from '@/utils/sounds';
import { Inertia } from '@inertiajs/inertia';



const redirecting = ref(false);

function openDoor(url) {
    if (redirecting.value) return;
    redirecting.value = true;

    const door = event.currentTarget;
    door.classList.add('door-open');
    door.classList.add('hide-elements');

    // playSound('door_open');

    setTimeout(() => {
        Inertia.visit(url);
    }, 1500);
}


function playHoverSound() {
    playSound('door_creak');
    const door = event.currentTarget;
    door.classList.add('hide-elements');

} function showElements() {
    const door = event.currentTarget;
    door.classList.remove('hide-elements');
}

onMounted(async () => {
    playSound('home_bg');
});


</script>

<template>

    <Head title="Dashboard" />
    <div class="min-h-screen bg-void-black flex items-center justify-center relative overflow-hidden">
        <div class="glitch-effect"></div>

        <div class="ghost ghost-1"></div>
        <div class="ghost ghost-2"></div>
        <div class="ghost ghost-3"></div>

        <div class="blood-drips"></div>

        <div class="flex gap-16 z-10 relative">
            <div class="castle-door blood-door" @click="openDoor('/chat-rooms')" @mouseenter="playHoverSound"
                @mouseleave="showElements">
                <div class="door-left"></div>
                <div class="door-right"></div>
                <div class="door-knocker">
                    <div class="ring"></div>
                    <div class="face">
                        <div class="eyes"></div>
                        <div class="mouth"></div>
                    </div>
                </div>
                <div class="door-plaque">
                    <span class="text-5xl font-creepster">Chat Rooms</span>
                    <span class="font-im-fell text-sm opacity-75">Where whispers never die...</span>
                </div>
                <div class="door-eyes">
                    <div class="eye"></div>
                    <div class="eye"></div>
                </div>
            </div>

            <div class="castle-door void-door" @click="openDoor('/files')" @mouseenter="playHoverSound"
                @mouseleave="showElements">
                <div class="door-left"></div>
                <div class="door-right"></div>
                <div class="door-knocker">
                    <div class="ring"></div>
                    <div class="face">
                        <div class="eyes"></div>
                        <div class="mouth"></div>
                    </div>
                </div>
                <div class="door-plaque">
                    <span class="text-5xl font-creepster">File Abyss</span>
                    <span class="font-im-fell text-sm opacity-75">Files vanish into the eternal void...</span>
                </div>
                <div class="door-eyes">
                    <div class="eye"></div>
                    <div class="eye"></div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 w-full h-32 bg-gradient-to-t from-black/80 to-transparent z-20"></div>
    </div>
</template>

<style>
.castle-door {
    width: 400px;
    height: 800px;
    position: relative;
    cursor: pointer;
    perspective: 1000px;
    transform-style: preserve-3d;
}

.door-left,
.door-right {
    position: absolute;
    width: 50%;
    height: 100%;
    background-color: #2a0000;
    border: 8px solid #4a0000;
    box-shadow: 0 0 40px #ff444433 inset;
    transition: transform 1.2s cubic-bezier(0.4, 0, 0.2, 1);
    backface-visibility: hidden;
}

.door-left {
    left: 0;
    transform-origin: left center;
    border-right: 4px solid #ff4444;
}

.door-right {
    right: 0;
    transform-origin: right center;
    border-left: 4px solid #ff4444;
}

.castle-door:hover .door-left {
    transform: rotateY(-15deg);
}

.castle-door:hover .door-right {
    transform: rotateY(15deg);
}

.castle-door.door-open .door-left {
    transform: rotateY(-90deg);
}

.castle-door.door-open .door-right {
    transform: rotateY(90deg);
}

.castle-door.hide-elements .door-eyes,
.castle-door.hide-elements .face,
.castle-door.hide-elements .ring {
    display: none;
}

.door-eyes {
    position: absolute;
    top: 30%;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 60px;
    z-index: 2;
}

.eye {
    width: 40px;
    height: 40px;
    background: #ff4444;
    border-radius: 50%;
    position: relative;
    animation: blink 5s infinite;
}

.eye::before {
    content: '';
    position: absolute;
    width: 15px;
    height: 15px;
    background: black;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

@keyframes blink {

    0%,
    96%,
    100% {
        height: 40px;
    }

    98% {
        height: 5px;
    }
}

.glitch-effect {
    position: absolute;
    width: 100%;
    height: 100%;
    background: url('/images/glitch-effect.png');
    opacity: 0.2;
    animation: glitch 2s infinite;
    z-index: 1;
    pointer-events: none;
}

@keyframes glitch {
    0% {
        transform: translate(0);
    }

    20% {
        transform: translate(-5px, 5px);
    }

    40% {
        transform: translate(5px, -5px);
    }

    60% {
        transform: translate(-5px, 5px);
    }

    80% {
        transform: translate(5px, -5px);
    }

    100% {
        transform: translate(0);
    }
}

.ghost {
    position: absolute;
    width: 200px;
    height: 200px;
    background: url('/images/ghost.png') no-repeat center/contain;
    opacity: 0.3;
    animation: float 10s infinite;
    z-index: 1;
}

.ghost-1 {
    top: 10%;
    left: 5%;
    animation-duration: 12s;
}

.ghost-2 {
    top: 30%;
    right: 5%;
    animation-duration: 15s;
}

.ghost-3 {
    top: 50%;
    left: 20%;
    animation-duration: 18s;
}

@keyframes float {
    0% {
        transform: translateY(0) rotate(0deg);
    }

    50% {
        transform: translateY(-50px) rotate(180deg);
    }

    100% {
        transform: translateY(0) rotate(360deg);
    }
}

.blood-drips {
    position: absolute;
    width: 100%;
    height: 100%;
    background: url('/images/blood-drips.png');
    opacity: 0.3;
    animation: drip 5s infinite;
    z-index: 1;
    pointer-events: none;
}

@keyframes drip {
    0% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(10px);
    }

    100% {
        transform: translateY(0);
    }
}

.door-knocker {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
}

.ring {
    width: 20px;
    height: 20px;
    border: 3px solid #ff4444;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    animation: knock 2s infinite;
}

.face {
    width: 30px;
    height: 30px;
    background: #4a0000;
    border-radius: 50%;
    position: relative;
    margin-top: 25px;
}

.eyes {
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 8px;
    background: #ff4444;
    border-radius: 50%;
}

.mouth {
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 4px;
    background: #ff4444;
    border-radius: 2px;
}

@keyframes knock {
    0% {
        transform: translateX(-50%) rotate(0deg);
    }

    25% {
        transform: translateX(-50%) rotate(15deg);
    }

    50% {
        transform: translateX(-50%) rotate(-15deg);
    }

    100% {
        transform: translateX(-50%) rotate(0deg);
    }
}

.door-plaque {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: white;
    z-index: 2;
    text-shadow: 0 0 10px #ff4444;
}

.blood-door .door-left,
.blood-door .door-right {
    background-image:
        radial-gradient(circle at center, #ff444410 0%, #4a000000 70%),
        url('/images/wood-texture-blood.jpg');
    border-color: #ff4444;
}

.void-door .door-left,
.void-door .door-right {
    background-image:
        radial-gradient(circle at center, #4444ff10 0%, #00052a00 70%),
        url('/images/wood-texture-void.jpg');
    border-color: #4444ff;
}


html {
    overflow-y: auto !important;
}
</style>
