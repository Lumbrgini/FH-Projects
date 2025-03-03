<script setup>
import { ref } from "vue";

let socket = null;

// reactive data
const name = ref("");
const text = ref("");
const isJoined = ref(false);
const messages = ref([]);

// event handler
function joinChat() {
  socket = io("http://localhost:3333");
  socket.on("message", (data) => {
    messages.value.push(data);
  });
  socket.emit("message", `${name.value} has joined the chat!`);
  isJoined.value = true;
}

function sendMessage() {
  if (text.value.trim() !== "") {
    socket.emit("message",`${name.value}: ${text.value}`);
    text.value = "";
  }
}

function leaveChat() {
  if (socket) {
    socket.emit("message", `${name.value} has left the chat.`);

    setTimeout(() => {
      socket.disconnect();
      socket = null;
      messages.value = [];
      isJoined.value = false;
    }, 100); 
  }
}


</script>


<template>
  <div>
    <h1>Chat Client</h1>

    <div v-if="!isJoined">
      <h2>Join the Chat</h2>
      <input v-model="name" placeholder="Enter your nickname" />
      <button @click="joinChat">Join</button>
    </div>

    <div v-else>
      <h2>{{ name }}</h2>
      <input v-model="text" placeholder="Enter a message" />
      <button @click="sendMessage">Send</button>
      <button @click="leaveChat">Leave</button>

      <p v-if="messages.length === 0">No messages yet.</p>
      <ul>
        <li v-for="msg in messages" :key="msg">{{ msg }}</li>
      </ul>
    </div>
  </div>
</template>



<style scoped>

</style>
