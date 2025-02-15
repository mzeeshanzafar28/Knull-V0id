<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <Head title="Awaken the Unseen" />

    <div class="verify-container">
        <div class="verify-box">
            <div class="blood-drip"></div>

            <AuthenticationCard class="mr-wrapper">
                <template #logo>
                    <AuthenticationCardLogo class="glitch" />
                </template>

                <div class="mb-4 text-sm text-gray-400 dark:text-gray-300 horror-text">
                    The shadows whisper—verify your presence. A spectral link awaits in your inbox. If it has vanished into the void, summon another.
                </div>

                <div v-if="status === 'verification-link-sent'" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    A new spectral link has been dispatched. Check your cryptic messages.
                </div>

                <form @submit.prevent="submit" class="dark-form">
                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton class="horror-button" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Summon Verification Link
                        </PrimaryButton>
                    </div>
                </form>
            </AuthenticationCard>
        </div>
    </div>
</template>

<style scoped>
.verify-container {
    background: radial-gradient(circle at center, #000000 40%, #110011, #220022, #330033);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Creepster', cursive;
    overflow: hidden;
    position: relative;
}

.blood-drip {
    position: absolute;
    top: 0;
    left: 50%;
    width: 100%;
    height: 150px;
    background: url('/images/blood-drip.png') no-repeat center top;
    background-size: cover;
    z-index: 1;
    animation: drip 6s infinite alternate;
}

@keyframes drip {
    0% { transform: translateY(0); }
    100% { transform: translateY(5px); }
}

.verify-box {
    background: rgba(10, 0, 20, 0.9);
    padding: 30px;
    border: 2px solid #ff0000;
    box-shadow: 0 0 25px #ff0000;
    border-radius: 10px;
    position: relative;
    animation: pulse 4s infinite alternate;
}

@keyframes pulse {
    0% { box-shadow: 0 0 15px #ff0000; }
    100% { box-shadow: 0 0 30px #ff4444; }
}

.horror-text {
    font-size: 1rem;
    color: #ff4444;
    text-shadow: 0 0 10px rgba(255, 0, 0, 0.8);
}

.dark-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.horror-button {
    background: linear-gradient(to right, #550000, #ff0000);
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-transform: uppercase;
    font-size: 1.2rem;
    font-weight: bold;
    letter-spacing: 2px;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 0 10px #ff0000;
}

.horror-button:hover {
    background: #ff4444;
    box-shadow: 0 0 20px #ff0000;
}

.glitch {
    animation: glitch 1s infinite;
    margin-top: -8rem;
}

@keyframes glitch {
    0% { transform: translate(0, 0); }
    20% { transform: translate(-2px, 2px); }
    40% { transform: translate(2px, -2px); }
    60% { transform: translate(-2px, 2px); }
    80% { transform: translate(2px, -2px); }
    100% { transform: translate(0, 0); }
}

.mr-wrapper {
    min-height: -webkit-fill-available !important;
}


.verify-box {
    height: -webkit-fill-available !important;
}


</style>
