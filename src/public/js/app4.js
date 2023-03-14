//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

let gumStream; 						//stream from getUserMedia()
let recorder; 						//WebAudioRecorder object
let input; 							//MediaStreamAudioSourceNode  we'll be recording
let encodingType; 					//holds selected encoding for resulting audio (file)
let encodeAfterRecord = true;       // when to encode

// shim for AudioContext when it's not avb.
let AudioContext = window.AudioContext || window.webkitAudioContext;
let audioContext; //new audio context to help us record
let analyser;

let activeFile = null;
let file1 = null;
let file2 = null;
let file3 = null;

let recordButton1 = document.getElementById("recordButton1");
let stopButton1 = document.getElementById("stopButton1");
let progress1 = document.getElementById("progress1");
let canvas1 = document.getElementById("canvas1");
let recordButton2 = document.getElementById("recordButton2");
let stopButton2 = document.getElementById("stopButton2");
let progress2 = document.getElementById("progress2");
let recordButton3 = document.getElementById("recordButton3");
let stopButton3 = document.getElementById("stopButton3");
let progress3 = document.getElementById("progress3");

let submitButton = document.getElementById("submitButton");
submitButton.disabled = true;
submitButton.classList.remove("btn-success");


let isRecordingStarted = false;
recordButton1.addEventListener("click", startRecording);
recordButton2.addEventListener("click", startRecording);
recordButton3.addEventListener("click", startRecording);

let counter = null
let counterSec = 30;
let counterFn = function () {
    // console.log(recorder.buffer);
    // console.log(recorder);

    if (activeFile == 1) {
        stopButton1.innerHTML = "Stop (" + counterSec + "s)";
    }
    if (activeFile == 2) {
        stopButton2.innerHTML = "Stop (" + counterSec + "s)";
    }
    if (activeFile == 3) {
        stopButton3.innerHTML = "Stop (" + counterSec + "s)";
    }
    counterSec--;
    if (counterSec < 0) counterSec = 0;
}

function startRecording(e) {
    isRecordingStarted = !isRecordingStarted;
    if(isRecordingStarted) {
        
    }
    document.querySelectorAll('button[data-bs-slide]').forEach(nextBtn => {
        nextBtn.disabled = true;
        nextBtn.classList.remove("btn-success");
    });

    activeFile = e.target.id.slice(-1);
    recordButton1.disabled = true;
    stopButton1.disabled = true;
    recordButton2.disabled = true;
    stopButton2.disabled = true;
    recordButton3.disabled = true;
    stopButton3.disabled = true;
    recordButton1.classList.remove("btn-danger");
    stopButton1.classList.remove("btn-danger");
    recordButton2.classList.remove("btn-danger");
    stopButton2.classList.remove("btn-danger");
    recordButton3.classList.remove("btn-danger");
    stopButton3.classList.remove("btn-danger");
    stopButton1.style.boxShadow = "none";
    stopButton2.style.boxShadow = "none";
    stopButton3.style.boxShadow = "none";

    if (activeFile == 1) {
        stopButton1.disabled = false;
        stopButton1.classList.add("btn-danger");
    }
    if (activeFile == 2) {
        stopButton2.disabled = false;
        stopButton2.classList.add("btn-danger");
    }
    if (activeFile == 3) {
        stopButton3.disabled = false;
        stopButton3.classList.add("btn-danger");
    }

    let constraints = {audio: true, video: false}
    navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
        audioContext = new AudioContext();
        gumStream = stream;
        input = audioContext.createMediaStreamSource(stream);

        analyser = audioContext.createAnalyser();
        const data_arr = new Uint8Array(analyser.frequencyBinCount);
        input.connect(analyser);
        const report = () => {
            analyser.getByteFrequencyData(data_arr);
            const volume = Math.floor((Math.max(...data_arr) / 255) * 100);
            progress1.style.width = volume + "%";
            progress2.style.width = volume + "%";
            progress3.style.width = volume + "%";

            if (volume){
                let s1 = 5+(volume/3);
                let s2 = 3 + (volume/4);
                stopButton1.style.boxShadow = "0 0 "+s1+"px "+s2+"px red";
                stopButton2.style.boxShadow = "0 0 "+s1+"px "+s2+"px red";
                stopButton3.style.boxShadow = "0 0 "+s1+"px "+s2+"px red";
            } else {
                stopButton1.style.boxShadow = "none";
                stopButton2.style.boxShadow = "none";
                stopButton3.style.boxShadow = "none";
            }
            if (input) requestAnimationFrame(report);
            else input.close();
        }
        report()
//
//         let CANVAS = document.getElementById("canvas1");
// // Keep reference to GSAP timeline
//         let timeline = gsap.timeline()
// // Generate Array for BARS
//         const BARS = []
// // Define a Bar width on the canvas
//         const BAR_WIDTH = 400
// // We can declare a fill style outside of the loop.
// // Let’s start with red!
//         const DRAWING_CONTEXT = CANVAS.getContext('2d');
//         DRAWING_CONTEXT.fillStyle = 'red'
// // Update our drawing function to draw a bar at the correct "x" accounting for width
// // Render bar vertically centered
//         const drawBar = ({ x, size }) => {
//             const POINT_X = x - BAR_WIDTH / 2
//             const POINT_Y = CANVAS.height / 2 - size / 2
//             DRAWING_CONTEXT.fillRect(POINT_X, POINT_Y, BAR_WIDTH, size)
//         }
// // drawBars updated to iterate through new letiables
//         const drawBars = () => {
//             DRAWING_CONTEXT.clearRect(0, 0, CANVAS.width, CANVAS.height)
//             for (const BAR of BARS) {
//                 drawBar(BAR)
//             }
//         }
//
//         timeline.clear();
//
//         const report = () => {
//
//             if (recorder) {
//                 analyser.getByteFrequencyData(data_arr)
//                 const VOLUME = Math.floor((Math.max(...data_arr) / 255) * 100)
//                 progress1.style.width = VOLUME + "%";
//
//                 // At this point create a bar and have it added to the timeline
//                 const BAR = {
//                     x: CANVAS.width + BAR_WIDTH / 2,
//                     size: gsap.utils.mapRange(0, 100, 5, CANVAS.height * 0.8)(VOLUME)
//                 }
//                 // Add to bars Array
//                 BARS.push(BAR)
//                 // Add the bar animation to the timeline
//                 timeline
//                     .to(BAR, {
//                         x: `-=${CANVAS.width + BAR_WIDTH}`,
//                         ease: 'none',
//                         duration: 10,//CONFIG.duration,
//                     })
//             }
//             if (recorder) {
//                 drawBars()
//             }
//             if (input) requestAnimationFrame(report);
//             else input.close();
//         }
//         report()


//         analyser.fftSize = 2048;
//         const bufferLength = analyser.frequencyBinCount;
//         const dataArray = new Uint8Array(bufferLength);
//         const WIDTH = 100;
//         const HEIGHT = 50;
//         analyser.getByteTimeDomainData(dataArray);
//
// // draw an oscilloscope of the current audio source
//
//         function draw() {
//             drawVisual = requestAnimationFrame(draw);
//
//             analyser.getByteTimeDomainData(dataArray);
//
//             let canvasCtx = canvas1.getContext('2d');
//
//             canvasCtx.fillStyle = "rgb(200, 200, 200)";
//             canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);
//
//             canvasCtx.lineWidth = 2;
//             canvasCtx.strokeStyle = "rgb(0, 0, 0)";
//
//             canvasCtx.beginPath();
//
//             const sliceWidth = (WIDTH * 1.0) / bufferLength;
//             let x = 0;
//
//             for (let i = 0; i < bufferLength; i++) {
//                 const v = dataArray[i] / 128.0;
//                 const y = (v * HEIGHT) / 2;
//
//                 if (i === 0) {
//                     canvasCtx.moveTo(x, y);
//                 } else {
//                     canvasCtx.lineTo(x, y);
//                 }
//
//                 x += sliceWidth;
//             }
//
//             canvasCtx.lineTo(canvas1.width, canvas1.height / 2);
//             canvasCtx.stroke();
//         }
//         draw();


        encodingType = 'mp3'
        recorder = new WebAudioRecorder(input, {
            workerDir: "js/",
            encoding: encodingType,
            numChannels: 2,
        });

        recorder.onComplete = function (recorder, blob) {
            submitButton.disabled = false;
            submitButton.classList.add("btn-success");

            clearInterval(counter);
            counterSec = 30;
            stopButton1.innerHTML = "Stop";
            stopButton2.innerHTML = "Stop";
            stopButton3.innerHTML = "Stop";

            stopButton1.style.boxShadow = "none";
            stopButton2.style.boxShadow = "none";
            stopButton3.style.boxShadow = "none";

            createDownloadLink(blob, recorder.encoding);
            if (activeFile == 1) {
                file1 = blob;
                recordButton1.innerHTML = "Record Again";
            }
            if (activeFile == 2) {
                file2 = blob;
                recordButton2.innerHTML = "Record Again";

            }
            if (activeFile == 3) {
                file3 = blob;
                recordButton3.innerHTML = "Record Again";
            }


            document.querySelectorAll('button[data-bs-slide]').forEach(nextBtn => {
                nextBtn.disabled = false;
                nextBtn.classList.add("btn-success");
            });
            recordButton1.disabled = false;
            stopButton1.disabled = true;
            recordButton1.classList.add("btn-danger");
            stopButton1.classList.remove("btn-danger");
            recordButton2.disabled = false;
            stopButton2.disabled = true;
            recordButton2.classList.add("btn-danger");
            stopButton2.classList.remove("btn-danger");
            recordButton3.disabled = false;
            stopButton3.disabled = true;
            recordButton3.classList.add("btn-danger");
            stopButton3.classList.remove("btn-danger");
        }

        // recorder.onDataavailable() = (e)=>{
        //     chunks.push(e.data);
        // }

        recorder.setOptions({
            timeLimit: 30,
            encodeAfterRecord: encodeAfterRecord,
            ogg: {quality: 0.5},
            mp3: {bitRate: 160}
        });

        recorder.startRecording();
        counterFn();
        counter = setInterval(counterFn, 1000);

    }).catch(function (err) {
        recordButton1.disabled = false;
        stopButton1.disabled = true;
        recordButton1.classList.add("btn-danger");
        stopButton1.classList.remove("btn-danger");
        recordButton2.disabled = false;
        stopButton2.disabled = true;
        recordButton2.classList.add("btn-danger");
        stopButton2.classList.remove("btn-danger");
        recordButton3.disabled = false;
        stopButton3.disabled = true;
        recordButton3.classList.add("btn-danger");
        stopButton3.classList.remove("btn-danger");
    });
}

function stopRecording() {
    gumStream.getAudioTracks()[0].stop();
    recorder.finishRecording();

    stopButton1.style.boxShadow = "none";
    stopButton2.style.boxShadow = "none";
    stopButton3.style.boxShadow = "none";

}

function createDownloadLink(blob, encoding, id) {
    var url = URL.createObjectURL(blob);
    var au = document.createElement('audio');
    au.controls = true;
    au.controlsList = "noplaybackrate nodownload";
    au.src = url;
    if (activeFile == 1) {
        recording1.innerHTML = '';
        recording1.appendChild(au);
    }
    if (activeFile == 2) {
        recording2.innerHTML = '';
        recording2.appendChild(au);
    }
    if (activeFile == 3) {
        recording3.innerHTML = '';
        recording3.appendChild(au);
    }
}

const myCarouselElement = document.querySelector('#carouselEOPD');
const carousel = new bootstrap.Carousel(myCarouselElement, {
    interval: false,
    wrap: false
})

function submitRecords() {
    var xhr = new XMLHttpRequest();
    xhr.onload = function (e) {
        if (this.readyState === 4) {
            // console.log("Server returned: ", e.target.responseText);
            recording1.innerHTML = '';
            recording2.innerHTML = '';
            recording3.innerHTML = '';

            recordButton1.innerHTML = "Record";
            recordButton2.innerHTML = "Record";
            recordButton3.innerHTML = "Record";

            file1 = file2 = file3 = null;
            submitButton.disabled = true;
            submitButton.classList.remove("btn-success");

            carousel.next();
        }
    };
    var fd = new FormData();
    if (!file1 && !file2 && !file3) {
        alert("");
    }
    if (file1) {
        fd.append("file1", file1, "file1.mp3");
    }
    if (file2) {
        fd.append("file2", file2, "file2.mp3");
    }
    if (file3) {
        fd.append("file3", file3, "file3.mp3");
    }
    xhr.open("POST", "/voice-analyst", true);
    xhr.send(fd);
}

function cancelRecords() {
    recording1.innerHTML = '';
    recording2.innerHTML = '';
    recording3.innerHTML = '';

    recordButton1.innerHTML = "Record";
    recordButton2.innerHTML = "Record";
    recordButton3.innerHTML = "Record";

    file1 = file2 = file3 = null;
    submitButton.disabled = true;
    submitButton.classList.remove("btn-success");

    carousel.to(0);
}
