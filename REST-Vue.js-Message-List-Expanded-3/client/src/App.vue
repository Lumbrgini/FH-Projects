<script setup>
import { ref, onBeforeMount } from "vue";
import MyLi from "./components/MyLi.vue";
import axios from "redaxios";

const API_URL = "/api";

const notesData = ref([]);
const name = ref("");
const surname = ref("");

async function saveData() {
    if (surname.value.trim().length === 0 && name.value.trim().length === 0) {
        name.value = "";
        surname.value = "";
        return;
    }

    const entry = {
        name: name.value.trim(),
        surname: surname.value.trim(),
    };

    try {
        const response = await axios.post(API_URL, entry);
        entry._id = response.data.insertedId;
        notesData.value.push(entry);
        name.value = "";
        surname.value = "";
    } catch (error) {
        console.error("Error saving data: ", error);
    }
}

async function clearEntry(index) {
    try {
        await axios.delete(`${API_URL}/${notesData.value[index]._id}`);
        notesData.value.splice(index, 1);
    } catch (error) {
        console.error("Error deleting data: ", error);
    }
}

async function clearData() {
    while(notesData.value.length > 0) {
        await clearEntry(0);
    }
}

async function saveEntry(index, newName, newSurname) {
    if (newName.trim().length > 0) {
        notesData.value[index].name = newName.trim();
    }
    if (newSurname.trim().length > 0) {
        notesData.value[index].surname = newSurname.trim();
    }
    try {
        await axios.put(`${API_URL}/${notesData.value[index]._id}`, {
            name: notesData.value[index].name,
            surname: notesData.value[index].surname,
        });
    } catch (error) {
        console.error("Error updating data: ", error);
    }
}


onBeforeMount( async () => {
    try {
        const response = await axios.get(API_URL);
        notesData.value = response.data;
    } catch (error) {
        console.error("Error fetching data: ", error);
    }
});


</script>

<template>
<div>
    <p v-if="notesData.length === 0">No notes yet.</p>  
    <ul v-else>
        <MyLi v-for="(entry, index) in notesData" :name="entry.name" :surname="entry.surname" :key="entry._id"
            @delete-me="clearEntry(index)" @save-me="saveEntry(index, $event)" />
    </ul>

    <textarea v-model="name"
              placeholder="Enter your name here..."
    @keyup.ctrl.enter="saveData"></textarea><br>
    <textarea v-model="surname"
              placeholder="Enter your surname here..."
    @keyup.ctrl.enter="saveData"></textarea><br>
    <button @click="saveData">Save</button>
    <button @click="clearData">Clear</button>
</div>
</template>

<style scoped>
</style>