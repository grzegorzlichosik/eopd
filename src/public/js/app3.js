//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

var gumStream; 						//stream from getUserMedia()
var recorder; 						//WebAudioRecorder object
var input; 							//MediaStreamAudioSourceNode  we'll be recording
var encodingType; 					//holds selected encoding for resulting audio (file)
var encodeAfterRecord = true;       // when to encode

// shim for AudioContext when it's not avb.
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext; //new audio context to help us record
var analyser;

var activeFile = null;
var file1 = null;
var file2 = null;
var file3 = null;

var recordButton1 = document.getElementById("recordButton1");
var stopButton1 = document.getElementById("stopButton1");
var recordButton2 = document.getElementById("recordButton2");
var stopButton2 = document.getElementById("stopButton2");
var recordButton3 = document.getElementById("recordButton3");
var stopButton3 = document.getElementById("stopButton3");

var submitButton = document.getElementById("submitButton");
submitButton.disabled = true;
submitButton.classList.remove("btn-success");

recordButton1.addEventListener("click", startRecording);
stopButton1.addEventListener("click", stopRecording);
recordButton2.addEventListener("click", startRecording);
stopButton2.addEventListener("click", stopRecording);
recordButton3.addEventListener("click", startRecording);
stopButton3.addEventListener("click", stopRecording);

var counter = null
var counterSec = 30;
var counterFn = function () {
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

    var constraints = {audio: true, video: false}
    navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
        audioContext = new AudioContext();
        gumStream = stream;
        input = audioContext.createMediaStreamSource(stream);

        // analyser = audioContext.createAnalyser();
        // const DATA_ARR = new Uint8Array(analyser.frequencyBinCount)
        // input.connect(analyser);

        // const REPORT = () => {
        //     // Copy the frequency data into DATA_ARR
        //     analyser.getByteFrequencyData(DATA_ARR)
        //     // If we are still recording, run REPORT again in the next available frame
        //     if (input) requestAnimationFrame(REPORT)
        //     else {
        //         // Else, close the context and tear it down.
        //         input.close()
        //     }
        // }
        // // Initiate reporting
        // REPORT()



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
}

function createDownloadLink(blob, encoding, id) {
    var url = URL.createObjectURL(blob);
    var au = document.createElement('audio');
    au.controls = true;
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
