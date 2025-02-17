<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const files = ref([]);
const uploadError = ref(null);
const isDragging = ref(false);
const fileInput = ref(null);

const fetchFiles = async () => {
    try {
        const response = await axios.post('/files/mine');
        files.value = response.data;
    } catch (error) {
        console.error('Failed to summon files:', error);
    }
};

const handleUpload = async (event) => {
    let file;

    if (event.target && event.target.files) {
        file = event.target.files[0];
    } else if (event.dataTransfer && event.dataTransfer.files) {
        file = event.dataTransfer.files[0];
    }

    if (!file) return;

    if (file.size > 100 * 1024 * 1024) {
        alert("Void Rejected your File: Size too large for initial void prototype.");
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    try {
        await axios.post('/files/upload', formData);
        await fetchFiles();
        uploadError.value = null;
    } catch (error) {
        if (error.response.status === 413) {
            alert('The void rejected your offering cz the stuff you are uploading is too large.');
            return;
        }
        console.log(error.response.data.message);
        uploadError.value = 'The void rejected your offering';
    }
};

const deleteFile = async (fileId) => {
    try {
        await axios.post(`/files/delete/${fileId}`);
        await fetchFiles();
    } catch (error) {
        console.error('Failed to erase file:', error);
    }
};

const handleDownload = async (fileId) => {
    try {
        const response = await axios.post('/files/download', { file_id: fileId }, { responseType: 'blob' });
        const blob = new Blob([response.data], { type: response.headers['content-type'] });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = response.headers['content-disposition'].split('filename=')[1];
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error('Download failed:', error);
        alert('No file found in the abyss with this ID');
    }
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        alert('File ID copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
};

const triggerFileInput = () => {
    fileInput.value.click();
};

onMounted(fetchFiles);
</script>

<template>
    <AppLayout title="Upload to the Void">
        <div class="mt-48 bg-void-black relative overflow-hidden">
            <div class="relative z-10 container mx-auto px-4 py-12">
                <div class="max-w-4xl mx-auto">
                    <!-- Upload Area -->
                    <div @click="triggerFileInput" @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false" @drop.prevent="isDragging = false; handleUpload($event)"
                        :class="{ 'border-blood-red': isDragging, 'border-blood-red/30': !isDragging }"
                        class="border-2 border-dashed rounded-lg p-8 mb-12 text-center cursor-pointer transition-colors">
                        <!-- Hidden File Input -->
                        <input type="file" ref="fileInput" class="hidden" @change="handleUpload" />

                        <div class="text-4xl mb-4">⬆️</div>
                        <p class="font-im-fell text-ghost-white">
                            Drag files here or click to summon from your realm
                        </p>
                        <p class="text-blood-red text-sm mt-2">
                            Files auto-vanish after 24 hours
                        </p>
                    </div>

                    <!-- Uploaded Files -->
                    <div class="bg-black/50 backdrop-blur-sm rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-black/30">
                                <tr>
                                    <th class="font-creepster text-blood-red p-4 text-left">File</th>
                                    <th class="font-creepster text-blood-red p-4">File ID</th>
                                    <th class="font-creepster text-blood-red p-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="file in files" :key="file.id"
                                    class="border-t border-blood-red/10 hover:bg-black/20 transition-colors">
                                    <td class="p-4 font-im-fell text-ghost-white">{{ file.original_name }}</td>
                                    <td class="p-4 text-center font-mono text-ghost-white custom-guy">
                                        {{ file.file_id }}
                                        <button @click="copyToClipboard(file.file_id)" class="ml-2 text-white bg-gray-700 px-2 py-1 rounded-md hover:bg-gray-600">
                                            📋
                                        </button>
                                    </td>
                                    <td class="p-4 text-center space-x-2 ">
                                        <div class="custom-guy">
                                            <button @click="handleDownload(file.file_id)" class="px-3 py-1 bg-blue-500 hover:bg-blue-700 border border-blue-700
                                                   text-white rounded-lg transition-colors">
                                            Download
                                        </button>
                                        <button @click="deleteFile(file.id)" class="px-3 py-1 bg-blood-red/20 hover:bg-blood-red/40 border border-blood-red
                                                   text-ghost-white rounded-lg transition-colors">
                                            Erase
                                        </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-guy {
    display: flex;
    justify-content: center;
    align-items: center;
}

button{
    margin: 5%;
}

</style>
