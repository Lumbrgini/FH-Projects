"use strict";

// TODO: Create an XMLHttpRequest object for the asynchronous request.
// TODO: Select the form and add a "submit" handler that initiates the request-
    const request = new XMLHttpRequest();
    const form = document.querySelector("form");
    form.addEventListener("submit", sendAJAXRequest);

// TODO: Create a method for sending the request.
// TODO: Use a FormData object (see lecture 10) for collecting the data from the form.
// TODO: Open a post request to the rest-server/public/adduser route. Use a relative URL from this directory
//  (you will need to  use ../ to go down two levels and then go into the rest-server directory).
// TODO: Add an event listener for the load event and trigger a method to handle the response.
// TODO: Send the request and include the POST data.
// TODO: Don't forget to prevent the default action so that the form isn't submitted regularly.
    function sendAJAXRequest(event) {
        const formData = new FormData(form);

        request.open("POST", "../../rest-server/public/adduser", true);
        request.addEventListener("load", handleResponse);
        request.send(formData);
        event.preventDefault();
        // FormData - Objekt erstellen um POST-Daten abzufragen
        // POST-Request öffnen auf rest-server/adduser
        //../../rest-server/public/adduser
        //Response type auf "json" setzen
        //Accept-Header auf "application/json"
        //Event-Listener für den "load" Event: handleResponse()
        //Requst senden und POST-Daten (FormData) übergeben
    }

// TODO: Create a method for handling the response
// TODO: Select the element with the ID "status". The information about success/failure goes in there.
// TODO: Fill in the status of the request. Show 3 things:
//  - Status code and status text (e.g., 201 Created).
//  - The text from the "result" field of the JSON response.
//  - The text from the "message" field of the JSON response.

function handleResponse() {
    const statusElement = document.getElementById("status");

    statusElement.innerHTML = '';
    const response = JSON.parse(request.responseText);
    // Проверка на успешный статус
    if (request.status === 201) {

        const statusText = `${request.status} ${request.statusText}`;
        const resultText = response.result ? response.result : "No result provided";
        const messageText = response.message ? response.message : "No message provided";

        const statusItem = document.createElement("h1");
        statusItem.textContent = `${statusText}`;
        statusElement.appendChild(statusItem);

        const resultItem = document.createElement("p");
        resultItem.textContent = `${resultText}`;
        statusElement.appendChild(resultItem);

        const messageItem = document.createElement("p");
        messageItem.textContent = `${messageText}`;
        statusElement.appendChild(messageItem);
    }
    if (request.status === 409) {

        const statusText = `${request.status} ${request.statusText}`;
        const resultText = response.result ? response.result : "No result provided";
        const messageText = response.message ? response.message : "No message provided";

        const statusItem = document.createElement("h1");
        statusItem.textContent = `${statusText}`;
        statusElement.appendChild(statusItem);

        const resultItem = document.createElement("p");
        resultItem.textContent = `${resultText}`;
        statusElement.appendChild(resultItem);

        const messageItem = document.createElement("p");
        messageItem.textContent = `${messageText}`;
        statusElement.appendChild(messageItem);
    }

    if (request.status === 400) {

        const statusText = `${request.status} ${request.statusText}`;
        const resultText = response.result ? response.result : "No result provided";
        const messageText = response.message ? response.message : "No message provided";

        const statusItem = document.createElement("h1");
        statusItem.textContent = `${statusText}`;
        statusElement.appendChild(statusItem);

        const resultItem = document.createElement("p");
        resultItem.textContent = `${resultText}`;
        statusElement.appendChild(resultItem);

        const messageItem = document.createElement("p");
        messageItem.textContent = `${messageText}`;
        statusElement.appendChild(messageItem);
    }
}