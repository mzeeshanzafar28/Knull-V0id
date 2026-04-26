// @/utils/sounds.js

const soundFiles = {
    terror: '/sounds/terror.mp3',
    help: '/sounds/help.mp3',
    home_bg: '/sounds/home-bg.mp3',
    door_creak: '/sounds/door-creak.mp3',
    door_open: '/sounds/door-open.mp3',
    file_operations: '/sounds/file-operations.mp3',
    upload: '/sounds/upload.mp3',
    download: '/sounds/download.mp3',
    list_rooms: '/sounds/list-rooms-bg.mp3',
    join_room: '/sounds/join-room.mp3',
    message_sent: '/sounds/message-sent.mp3',
    message_received: '/sounds/message-received.mp3',
    drums_of_doom: '/sounds/drums-of-doom.mp3',
    hell_board: '/sounds/hell-board.mp3',
    users: '/sounds/users.mp3',
    god_user_chatrooms: '/sounds/god-user-chatrooms.mp3',
    chatroom: '/sounds/chatroom.mp3',

};

export async function playSound(type) {
    try {
        if (typeof Audio === 'undefined') {
            console.warn('Audio not supported in this environment');
            return;
        }

        const audio = new Audio(soundFiles[type]);
        audio.volume = 1.0;

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
