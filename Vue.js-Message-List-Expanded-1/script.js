"use strict";
/*
 * Hypermedia Systems & Architecture
 * http://www.fh-ooe.at/mtd
 *
 * Simple Vue.js 3 Application Template
 *
 */

const STORAGE_KEY = "hyp3ue2";

Vue.createApp({
    data() {
        return {
		    notesData:[],
            contents: "",
            message: "",
            newStyle: false,
        }
	},
    beforeMount(){
        if(this.notesData[0] == undefined){
            this.message = "Nothing here yet...";
        }
    },
    methods: {
        saveData(){ 
            if(this.contents.length !== 0){
                this.message = "";
            }
            if(this.contents.trim().length === 0){
                this.contents = "";
                return;
            }

            const entry = {
                date: new Date().toLocaleString(),
                contents: this.contents.trim(),
            };

            this.notesData.push(entry);
            this.contents = "";

            localStorage.setItem(STORAGE_KEY,
                JSON.stringify(this.notesData),
            )
        },
        
        clearData(){
            localStorage.clear();
            this.notesData = [];
            if(this.notesData[0] == undefined){
                this.message = "Nothing here yet...";
            }
        },
        clearOneEntry(i){
            this.notesData.splice(i, 1);
            localStorage.setItem(STORAGE_KEY,JSON.stringify(this.notesData));
            if(this.notesData[0] == undefined){
                this.message = "Nothing here yet...";
            }
        },
        changeStyle(){
            this.newStyle = !this.newStyle;
            let listElement = document.querySelector("ul").style;
            if(this.newStyle == true){
                listElement.fontFamily = "'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif";
                listElement.color = "red";
            } else {
                listElement.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                listElement.color = "black";
            }
        },

    },
    created(){
        const data = localStorage.getItem(STORAGE_KEY);
        if(data){
            this.notesData = JSON.parse(data);
        }
    },
}).mount('#app');

