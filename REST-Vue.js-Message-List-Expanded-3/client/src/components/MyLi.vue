<script setup>
import { ref, nextTick } from 'vue';

const props = defineProps(['name', 'surname']);
const emit = defineEmits(['delete-me', 'save-me']);

const editingName = ref(false);
const editingSurname = ref(false);
const myName = ref(props.name);
const mySurname = ref(props.surname);

const myNameInput = ref(null);
const mySurnameInput = ref(null);

function editMyName() {
    editingName.value = true;
    nextTick(() => myNameInput.value.select());
}

function saveMyName() {
    editingName.value = false;
    emit('save-me', { name: myName.value, surname: mySurname.value });
}

function editMySurname() {
    editingSurname.value = true;
    nextTick(() => mySurnameInput.value.select());
}

function saveMySurname() {
    editingSurname.value = false;
    emit('save-me', { name: myName.value, surname: mySurname.value });
}
</script>

<template>
    <li :class="{ editing: editingName || editingSurname }">
        <button @click="$emit('delete-me')">x</button>

        <span class="view" @dblclick="editMyName">{{ myName }}</span>
        <input v-if="editingName" class="edit"  @blur="saveMyName"  v-model="myName" ref="myNameInput"> -- 

        <span class="view" @dblclick="editMySurname">{{ mySurname }}</span>
        <input v-if="editingSurname" class="edit" @blur="saveMySurname" v-model="mySurname" ref="mySurnameInput"
        >
    </li>
</template>

<style scoped>
.edit {
    display: none;
}

li.editing .edit {
    display: inline;
}

li.editing .view {
    display: none;
}
</style>
