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
let activeBtn = null;
let file1 = null;
let file2 = null;
let file3 = null;

let recordButton1 = document.getElementById("recordButton1");
let recordButton2 = document.getElementById("recordButton2");
let recordButton3 = document.getElementById("recordButton3");
let progress1 = document.getElementById("progress1");
let progress2 = document.getElementById("progress2");
let progress3 = document.getElementById("progress3");

let recording1 = document.getElementById("recording1");
let recording2 = document.getElementById("recording2");
let recording3 = document.getElementById("recording3");
let recording11 = document.getElementById("recording11");
let recording22 = document.getElementById("recording22");
let recording33 = document.getElementById("recording33");

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
    activeBtn.innerHTML = "Stop (" + counterSec + "s)";
    counterSec--;
    if (counterSec < 0) {
        counterSec = 0;
        startRecording(); // <- this is stopping
    }
}

function startRecording(e) {
    isRecordingStarted = !isRecordingStarted;
    if (isRecordingStarted && e) {
        document.querySelectorAll('button[data-bs-slide]').forEach(nextBtn => {
            nextBtn.disabled = true;
            nextBtn.classList.remove("btn-success");
        });

        activeBtn = e.target;
        activeFile = e.target.id.slice(-1);

        activeBtn.blur();

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

                if (volume) {
                    let s1 = (volume / 3);
                    let s2 = (volume / 5);
                    activeBtn.style.boxShadow = "0 0 " + s1 + "px " + s2 + "px red";
                } else {
                    activeBtn.style.boxShadow = "none";
                }

                if (input) requestAnimationFrame(report);
                else input.close();
            }
            report()

            encodingType = 'mp3'
            recorder = new WebAudioRecorder(input, {
                workerDir: "js/",
                encoding: encodingType,
                numChannels: 2,
            });

            recorder.onComplete = function (recorder, blob) {
                console.log('onComplete');
                submitButton.disabled = false;
                submitButton.classList.add("btn-success");

                clearInterval(counter);

                counterSec = 30;
                activeBtn.innerHTML = "Record Again";

                createDownloadLink(blob, recorder.encoding);
                if (activeFile == 1) {
                    file1 = blob;
                }
                if (activeFile == 2) {
                    file2 = blob;

                }
                if (activeFile == 3) {
                    file3 = blob;
                }

                document.querySelectorAll('button[data-bs-slide]').forEach(nextBtn => {
                    nextBtn.disabled = false;
                    nextBtn.classList.add("btn-success");
                });

                console.log('onComplete -- finished');
            }

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
        });


    } else {
        gumStream.getAudioTracks()[0].stop();
        recorder.finishRecording();
        activeBtn.style.boxShadow = "none";
    }

    activeBtn.blur();
}

function createDownloadLink(blob, encoding, id) {
    var url = URL.createObjectURL(blob);
    var au = document.createElement('audio');
    au.controls = true;
    au.controlsList = "noplaybackrate nodownload";
    au.src = url;
    console.log(activeFile);
    if (activeFile == 1) {
        recording1.innerHTML = '';
        recording1.appendChild(au);
        recording11.innerHTML = recording1.innerHTML;
    }
    if (activeFile == 2) {
        recording2.innerHTML = '';
        recording2.appendChild(au);
        recording22.innerHTML = recording2.innerHTML;
    }
    if (activeFile == 3) {
        recording3.innerHTML = '';
        recording3.appendChild(au);
        recording33.innerHTML = recording3.innerHTML;
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
            recording11.innerHTML = '';
            recording22.innerHTML = '';
            recording33.innerHTML = '';

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
    recording11.innerHTML = '';
    recording22.innerHTML = '';
    recording33.innerHTML = '';

    recordButton1.innerHTML = "Record";
    recordButton2.innerHTML = "Record";
    recordButton3.innerHTML = "Record";

    file1 = file2 = file3 = null;
    submitButton.disabled = true;
    submitButton.classList.remove("btn-success");

    carousel.to(0);
}
