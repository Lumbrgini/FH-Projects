"use strict";
/*
 * Hypermedia Systems & Architecture
 * http://www.fh-ooe.at/mtd
 *
 * Simple Vue.js 3 Application Template
 *
 */


Vue.createApp({
    data() {
        return {}
	},
    methods: {
        startDraw() {
            const r = ~~(Math.random()*255);  // Math.floor()
            const g = ~~(Math.random()*255);  // Math.floor()
            const b = ~~(Math.random()*255);  // Math.floor()

            // zB. "rgb(23,178,201)"
            this.ctx.fillStyle = this.ctx.strokeStyle = `rgb(${r},${g},${b})`;

            this.drawing = true;
        },
        stopDraw() {
            this.drawing = false;
        },
        doDraw(e) {
            if (this.drawing) {
                this.ctx.beginPath();
                this.ctx.arc(
                    e.clientX-this.$refs.drawCanvas.offsetLeft,
                    e.clientY-this.$refs.drawCanvas.offsetTop,
                    5, 0, 2*Math.PI);
                this.ctx.fill();
            }
        },
      
        startPrimes() {
            this.worker.postMessage("start");
        },

        stopPrimes() {
            this.worker.postMessage("stop");
        },

        resetPrimes() {
            this.worker.postMessage("reset");
        },

        workerResponse(e) {
            this.$refs.primes.innerHTML = e.data;  
        }                                        

    },
    mounted() {
        this.ctx = this.$refs.drawCanvas.getContext('2d');
        const img = document.createElement('img');

        //const self = this;
        //img.onload = function() {
        //    self.ctx.drawImage(img,0,0,400,300);  // CLOSURE
        //}

        img.onload = () => {
            this.ctx.drawImage(img,0,0,400,300);
        }

        img.src = "fhlogo.png";  // ASYNCHRONOUS OPERATION !!!
        
        // this.ctx.drawImage(img,0,0,400,300);

        this.drawing = false;

        this.worker = new Worker("primeWorker.js");
        this.worker.onmessage = this.workerResponse;
    },
}).mount('#app');
