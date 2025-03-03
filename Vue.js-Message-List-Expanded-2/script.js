"use strict";
/*
 * Hypermedia Systems & Architecture
 * http://www.fh-ooe.at/mtd
 *
 * Simple Vue.js 3 Application Template
 *
 */

const STORAGE_KEY = "hyp3t1.lab2.g1.2024";

class Person {
    constructor(vorname, nachname) {
        this.vorname = vorname;
        this.nachname = nachname;
    }
}

const app = Vue.createApp({
    data() {
        return {
            personsData: [],
            newVorname: "",
            newNachname: "",
        };
    },
    methods: {
        addPerson() {
            if (this.newVorname.trim() && this.newNachname.trim()) {
                const person = new Person(this.newVorname.trim(), this.newNachname.trim());
                this.personsData.push(person);
                this.newVorname = ""; 
                this.newNachname = "";  
                localStorage.setItem(STORAGE_KEY, JSON.stringify(this.personsData));
            }
        },

        clearEntry(index) {
            this.personsData.splice(index, 1);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(this.personsData));
        },

        clearAll() {
            this.personsData = [];
            localStorage.setItem(STORAGE_KEY, JSON.stringify(this.personsData));
        },

        saveEntry(index, newNamen) {
            this.personsData[index].vorname = newNamen.vorname;
            this.personsData[index].nachname = newNamen.nachname;
            localStorage.setItem(STORAGE_KEY, JSON.stringify(this.personsData));
        },
    },
    created() {
        const data = localStorage.getItem(STORAGE_KEY);

        if (data) {
            this.personsData = JSON.parse(data);
            if (!Array.isArray(this.personsData)) this.personsData = [];
        }
    },
});

app.component("my-li", {
    props: ["vorname", "nachname"],
    emits: ["delete-me", "save-me"],
    data() {
        return {
            myVorname: this.vorname,
            myNachname: this.nachname,
            editingVorname: false,
            editingNachname: false,
        };
    },
    methods: {
        editMeVorname() {
            this.editingVorname = true;
            this.$nextTick(() => this.$refs.myInputVorname.select());
        },
        saveMeVorname() {
            this.editingVorname = false;
            this.$emit("save-me", {
                vorname: this.myVorname,
                nachname: this.myNachname
            });
        },
        editMeNachname() {
            this.editingNachname = true;
            this.$nextTick(() => this.$refs.myInputNachname.select());
        },
        saveMeNachname() {
           this.editingNachname = false;
            this.$emit("save-me", {
                vorname: this.myVorname,
                nachname: this.myNachname
            });
        },
    },
    template: "#mytemplate",
});

app.mount("#app");
