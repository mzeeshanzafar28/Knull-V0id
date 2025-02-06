<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { playSound } from '@/utils/sounds';
import { Head, Link } from '@inertiajs/vue3';

function playUploadSound() {
    playSound('upload');
}

function playDownloadSound() {
    playSound('download');
}
</script>

<template>
    <AppLayout title="File Abyss ">
        <div class="pt-60 flex items-center justify-center relative overflow-hidden">
            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 z-10">
                <!-- Upload Card -->
                <div class="w-96 h-44 bg-black/50 backdrop-blur-sm border-2 border-blood-red rounded-lg p-8 
                          hover:border-blood-red/70 transition-all hover:transform hover:-translate-y-2
                          shadow-void-glow hover:shadow-void-glow-intense cursor-pointer"
                    @click="playUploadSound(); $inertia.visit(route('filesUpload'))">
                    <h2 class="text-3xl font-creepster text-blood-red mb-4">Upload to the Void</h2>
                    <p class="font-im-fell text-ghost-white ">
                        Send your files into the eternal abyss...
                    </p>
                </div>

                <!-- Download Card -->
                <div class="w-96 h-44 bg-black/50 backdrop-blur-sm border-2 border-blood-red rounded-lg p-8 
                          hover:border-blood-red/70 transition-all hover:transform hover:-translate-y-2
                          shadow-void-glow hover:shadow-void-glow-intense cursor-pointer"
                    @click="playDownloadSound(); $inertia.visit(route('filesDownload'))">
                    <h2 class="text-3xl font-creepster text-blood-red mb-4">Retrieve from the Void</h2>
                    <p class="font-im-fell text-ghost-white">
                        Summon files back from the darkness...
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.shadow-void-glow {
    box-shadow: 0 0 25px rgba(255, 68, 68, 0.4);
}

.hover\:shadow-void-glow-intense:hover {
    box-shadow: 0 0 40px rgba(255, 68, 68, 0.6);
}

.bg-void-black {
    background-color: #0a0a0a;
}

.text-blood-red {
    color: #ff4444;
}

.text-ghost-white {
    color: #f0f0f0;
}

.font-creepster {
    font-family: 'Creepster', cursive;
}

.font-im-fell {
    font-family: 'IM Fell English SC', serif;
}
</style>
