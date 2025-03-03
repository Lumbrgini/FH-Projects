"use strict";

const io = require("socket.io")(3000, {
    cors: {
        origin: "*",
    },
});

let clientSockets = [];

function messageHandler(data) {
    for (let socket of clientSockets) {
        socket.send(data);
    }
    console.log(data);
}

io.on("connect", socket => {
    clientSockets.push(socket);
    socket.on("message", messageHandler);
});

console.log("Chat Server listening on port 3000/3333.");