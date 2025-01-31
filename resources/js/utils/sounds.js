// @/utils/sounds.js

const soundFiles = {
    terror: '/sounds/distorted-sound-01-brvhrtz-248242.mp3',
    help: '/sounds/help-74926.mp3',
    door_creak: '/sounds/door-creak.mp3',
    door_open: '/sounds/door-open.mp3',
};

export async function playSound(type) {
    try {
        if (typeof Audio === 'undefined') {
            console.warn('Audio not supported in this environment');
            return;
        }

        const audio = new Audio(soundFiles[type]);
        audio.volume = 0.3;

        try {
            await audio.play();
        } catch (err) {
            document.addEventListener('click', async () => {
                try {
                    await audio.play();
                } catch (err) {
                    console.error('Failed to play sound:', err);
                }
            }, { once: true });
        }
    } catch (err) {
        console.error('Error playing sound:', err);
    }
}