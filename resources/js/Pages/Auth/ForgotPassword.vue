<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Summon the Forgotten" />

    <div class="forgot-container">
        <div class="forgot-box">
            <div class="blood-drip"></div>

            <AuthenticationCard class="mr-wrapper">
                <template #logo>
                    <AuthenticationCardLogo class="glitch" />
                </template>

                <div class="mb-4 text-sm text-gray-400 dark:text-gray-300 horror-text">
                    Lost in the abyss? No problem. Whisper your email, and the void shall send you a message from the underworld.
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="dark-form">
                    <div class="input-group">
                        <InputLabel for="email" value="Mark of the Forgotten (Email)" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full shadow-input"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton class="horror-button" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Summon Reset Link
                        </PrimaryButton>
                    </div>
                </form>
            </AuthenticationCard>
        </div>
    </div>
</template>

<style scoped>
/* Background with eerie atmosphere */
.forgot-container {
    background: radial-gradient(circle at center, #000000 40%, #110011, #220022, #330033);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Creepster', cursive;
    overflow: hidden;
    position: relative;
}

/* Floating blood-drip effect */
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

/* Centered reset password box */
.forgot-box {
    background: rgba(10, 0, 20, 0.9);
    padding: 30px;
    border: 2px solid #ff0000;
    box-shadow: 0 0 25px #ff0000;
    border-radius: 10px;
    position: relative;
    animation: pulse 4s infinite alternate;
    height: 100vh;
}

@keyframes pulse {
    0% { box-shadow: 0 0 15px #ff0000; }
    100% { box-shadow: 0 0 30px #ff4444; }
}

/* Horror-themed text */
.horror-text {
    font-size: 1rem;
    color: #ff4444;
    text-shadow: 0 0 10px rgba(255, 0, 0, 0.8);
}

/* Form input fields */
.dark-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.input-group {
    position: relative;
}

.input-group label {
    color: #ff4444;
    font-size: 1.2rem;
}

/* Glowing effect on input */
.shadow-input {
    background: #222;
    color: #fff;
    border: 2px solid #ff0000;
    padding: 10px;
    font-size: 1rem;
    box-shadow: 0 0 15px rgba(255, 0, 0, 0.6);
    transition: all 0.3s ease-in-out;
}

.shadow-input:focus {
    box-shadow: 0 0 25px rgba(255, 0, 0, 1);
    outline: none;
    background: #330000;
}

/* Horror-styled button */
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

/* Glitching logo */
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

</style>
