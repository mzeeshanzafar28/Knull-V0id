<template>
    <div>
      <h1>Chat Rooms</h1>
      <ul>
        <li v-for="room in chatRooms" :key="room.id">
          <a :href="`/chat-rooms/${room.id}`">{{ room.name }}</a>
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  import { defineComponent, toRefs } from 'vue';
  
  export default defineComponent({
    props: {
      chatRooms: {
        type: Array,
        required: true,
      },
    },
    setup(props) {
      const { chatRooms } = toRefs(props);
      return {
        chatRooms,
      };
    },
  });
  </script>
  