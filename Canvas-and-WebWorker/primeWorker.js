"use strict";

let running = false;  
let p = 0;            
let n = 1;           
let timer;          

onmessage = (e) => {
    if (e.data === "start") {
        if (!running) {  
            running = true;
            run(); 
        }
    } else if (e.data === "stop") {
        running = false;
        clearTimeout(timer); 

    } else if (e.data === "reset") {
        running = false;
        p = 0;
        n = 2;
        clearTimeout(timer); 
        postMessage(n);
    }
};

const X = 10; 

function run() {
    if (!running) return; 

    n++;
    let isPrime = true;

    for (let i = 2; i * i <= n; i++) {
        if (n % i === 0) {
            isPrime = false;
            break;
        }
    }

    if (isPrime) {
        p++;
        if (p % X === 0) {
            postMessage(n); 
        }
    }

    timer = setTimeout(run, 0);
}
