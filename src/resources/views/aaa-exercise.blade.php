@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="carouselEOPD" class="carousel carousel-fade">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <h1 class="text-center">Exercise 1</h1>
                            <div class="row pt-5 pb-5">
                                <div class="col-6"><p>Instructions.....</p></div>
                                <div class="col-6">
                                    <audio controls>
                                        <source src="/mp3/VoiceAnalyst1.ogg" type="audio/ogg">
                                        <source src="/mp3/VoiceAnalyst1.mp3" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            </div>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button type="button" class="btn btn-lg btn-danger form-control" id="recordButton1">Record</button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-lg form-control btn-lg" id="stopButton1" disabled>Stop</button>
                                </div>
                                <div class="col-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="next" class="btn btn-lg btn-success form-control" role="button">Next</button>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <h1 class="text-center">Exercise 2</h1>
                            <div class="row pt-5 pb-5">
                                <div class="col-6"><p>Instructions.....</p></div>
                                <div class="col-6">
                                    <audio controls>
                                        <source src="/mp3/VoiceAnalyst2.ogg" type="audio/ogg">
                                        <source src="/mp3/VoiceAnalyst2.mp3" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            </div>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button type="button" class="btn btn-lg btn-danger form-control" id="recordButton2">Record</button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-lg form-control" id="stopButton2" disabled>Stop</button>
                                </div>
                                <div class="col-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="next" class="btn btn-lg btn-success form-control" role="button">Next</button>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <h1 class="text-center">Exercise 3</h1>
                            <div class="row pt-5 pb-5">
                                <div class="col-6"><p>Instructions.....</p></div>
                                <div class="col-6">
                                    <audio controls>
                                        <source src="/mp3/VoiceAnalyst3.ogg" type="audio/ogg">
                                        <source src="/mp3/VoiceAnalyst3.mp3" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            </div>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button type="button" class="btn btn-lg btn-danger form-control" id="recordButton3">Record</button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-lg form-control" id="stopButton3" disabled>Stop</button>
                                </div>
                                <div class="col-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="next" class="btn btn-lg btn-success form-control" role="button">Next</button>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <h1 class="text-center">Summary</h1>
                            <div class="row p-5">
                                <div class="col-6">Exercise 1 Recording</div>
                                <div class="col-6">
                                    <div id="recording1"></div>
                                </div>
                            </div>
                            <div class="row p-5">
                                <div class="col-6">Exercise 2 Recording</div>
                                <div class="col-6">
                                    <div id="recording2"></div>
                                </div>
                            </div>
                            <div class="row p-5">
                                <div class="col-6">Exercise 3 Recording</div>
                                <div class="col-6">
                                    <div id="recording3"></div>
                                </div>
                            </div>

                            <div class="row p-5">
                                <div class="col-4">
                                    <button class="btn btn-lg btn-warning form-control" role="button" onclick="carousel.to(0);">CANCEL</button>
                                </div>
                                <div class="col-4 offset-4">
                                    <button class="btn btn-lg btn-success form-control" role="button" onclick="submitRecords()">SUBMIT</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/app.js"></script>
    <script src="/js/WebAudioRecorder.min.js"></script>
@endsection
