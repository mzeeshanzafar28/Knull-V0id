<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import axios from 'axios';

const fileId = ref('');
const downloadError = ref(null);

const handleDownload = async () => {
    try {
        const response = await axios.post('/files/download',
            { file_id: fileId.value },
            { responseType: 'blob' } // <-- Ensures binary response handling
        );

        // Create a Blob from the response
        const blob = new Blob([response.data], { type: response.headers['content-type'] });

        // Create a link element to trigger the download
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = response.headers['content-disposition'].split('filename=')[1]; // Extracts filename
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        downloadError.value = null;
    } catch (error) {
        downloadError.value = 'No file found in the abyss with this ID';
        console.error('Download failed:', error);
    }
};


</script>

<template>
    <AppLayout title="Retrieve from the Void">

        <div class="mt-48 bg-void-black relative overflow-hidden">
            <div class="relative z-10 container mx-auto px-4 py-12">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-black/50 backdrop-blur-sm border-2 border-blood-red/30 rounded-lg p-8">
                        <h2 class="text-2xl font-creepster text-blood-red mb-6 text-center">
                            Summon File from the Abyss
                        </h2>

                        <div class="space-y-4">
                            <input v-model="fileId" placeholder="Enter File ID..." class="w-full bg-black/30 border border-blood-red/50 rounded-lg px-4 py-3 
                                   font-im-fell text-ghost-white focus:border-blood-red focus:ring-blood-red/50" />

                            <button @click="handleDownload" class="w-full bg-blood-red/20 hover:bg-blood-red/40 border border-blood-red 
                                   text-ghost-white py-3 px-6 rounded-lg font-im-fell transition-colors">
                                Download from the Void
                            </button>

                            <p v-if="downloadError" class="text-blood-red text-center">
                                {{ downloadError }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>