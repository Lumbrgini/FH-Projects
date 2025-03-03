"use strict";

let notesData = [];

const STORAGE_KEY = "HYP3_LC";

const notesDiv = document.querySelector("#notes");
const contentsArea = document.querySelector("#contents");
const clearBtn = document.querySelector("#btnClear");

function saveData(){
  if(contentsArea.value.trim().length === 0) return;

  const entry = {
    date: new Date().toLocaleString(),
    contents: contentsArea.value
  }

  notesData.push(entry);
  contentsArea.value = "";

  renderData();
  localStorage.setItem(STORAGE_KEY, JSON.stringify(notesData));
}

function renderData(){
  let output = "";

  if(notesData.length != 0){
    notesDiv.style.borderStyle = "outset";
    notesDiv.style.borderWidth = "3px";
  } else {
    notesDiv.style.borderStyle = "none"; 
  }
  
  //for(let entry of notesData) {
  for(let i in notesData){

    output += 
    `<button id = "btnY" onclick = "removeEntry(${i})">X</button>
    <style> 
      #btnY {
      background-color: rgb(255, 228, 76);
      border-width: 1px;
      border-radius: 3px;
      width: 22px;}

      #btnY:hover{
      background-color:black;
      color: rgb(255, 228, 76);
      }
    </style>
    <em>${notesData[i].date}</em> -- 
    ${notesData[i].contents}<br>`;
   /* `<button>X</button
    <em>${entry.date}</em> -- 
    ${entry.contents}<br>`;
*/
  }
  notesDiv.innerHTML = output;
}

function loadData(){

  const data = localStorage.getItem(STORAGE_KEY);
  if(data) {
    notesData = JSON.parse(data);
  }
  renderData();
}

function removeEntry(i){
  notesData.splice(i, 1);
  localStorage.setItem(STORAGE_KEY,JSON.stringify(notesData));
  renderData();
}

function clearAll(){
  localStorage.clear();
  notesData = []; 
  renderData();
}

clearBtn.addEventListener("click", clearAll);
document.querySelector("#btn").addEventListener("click", saveData);
loadData();

console.log(notesData);
