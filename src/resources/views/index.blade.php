@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="carouselEOPD" class="carousel slide">
                    <div class="carousel-inner">
                        <div class="carousel-item @if(!$errors->any()) active @endif">
                            <h1 class="text-center">EOPD Voice Biometric Study</h1>
                            <h5 class="text-center">Welcome to the EOPD Voice Biometric Study which is being conducted in collaboration with the University Of Limerick.</h5>

                            <h2>Background</h2>
                            <p>Our names are Gavin O’Duffy, Isobel Cuddigan and JP Swaine and we are currently conducting our research project as part of our MSc in Digital Health Transformation in the University of Limerick. If you are
                                over 18, a
                                proficient English speaker and have a diagnosis of Parkinson’s Disease we should appreciate your participation in our study.</p>
                            <h3>What's the project about?</h3>
                            <p>This study will examine the measurement of voice and speech biomarkers to help track the progression of Parkinson’s Disease. The purpose of this project is to see if effective monitoring of speech and
                                voice can be
                                conducted by individuals with Parkinson’s Disease in their own homes.</p>
                            <h3>What will I need to do?</h3>
                            <p>This research study will involve participants with Parkinson’s Disease audio recording themselves using a phone, tablet, laptop or computer while reading a passage of text and completing specific speech
                                tasks (saying
                                ‘ahhhh’ for as long as possible and repeating “pat-a-cake” as many times as they can). These tasks will take less than 10 minutes and participants will be asked to complete them just twice in a 6 week
                                period.</p>
                            <h3>How is my data protected?</h3>
                            <p>Any data collected will remain completely confidential and only used for this study. Data will be stored securely. No identifying information will be used, you will only be identifiable by your sex and
                                age. Ethical
                                approval for the project has been granted by the Faculty of Science & Engineering Research Ethics Committee. If you would like some more information, please email Isobel Cuddigan <a
                                    href="mailto:21370745@studentmail.ul.ie">21370745@studentmail.ul.ie</a>. Our supervisor, <b>Annette McElligott</b>, Lecturer, University of Limerick, can be contacted via email at <a
                                    href="mailto:annette.mcelligott@ul.ie">annette.mcelligott@ul.ie</a>.</p>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button class="btn btn-warning btn-lg form-control" role="button" onclick="window.history.back()">Go Back</button>
{{--                                    <a href="https://eopd.ie/" class="btn btn-warning btn-lg form-control" role="button">Go Back</a>--}}
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('login') }}" class="btn btn-success btn-lg form-control" role="button">Log In</a>
                                </div>
                                <div class="col-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="next" class="btn btn-success btn-lg form-control" role="button">Sign Up</button>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item @if($errors->any()) active @endif">
                            <h1 class="text-center">EOPD Voice Biometric Study</h1>
                            <h2 class="text-center mb-5">Participant Questionnaire</h2>

                            <p style="color:grey;">THIS WILL NEED TO IMPLEMENTED USING AN ONLINE FORM, WHICH WILL BE STORED WITH THE CONSENT FORM</p>
                            <form action="{{ route('postRegistration') }}" method="POST">
                                @csrf

                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Age" id="age" class="form-control" name="age" autofocus>
                                </div>
                                <div class="form-group mb-3">
                                    <select id="sex" class="form-control" name="sex" autofocus>
                                        <option disabled selected="selected">Sex (Male/Female/Transgender/Prefer not to say)</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Transgender">Transgender</option>
                                        <option value="Prefer not to say">Prefer not to say</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="How long since you were diagnosed with Parkinson’s Disease?" id="since" class="form-control" name="since" autofocus>
                                </div>
                                <div class="form-group mb-3">
                                    <select id="medication" class="form-control" name="medication" autofocus>
                                        <option disabled selected="selected">Are you taking medication for your Parkinson’s Disease (Yes or No) ?</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="How frequently and at what times do you take your medication for Parkinson’s Disease?" id="frequency" class="form-control" name="frequency" autofocus>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Have you noticed a deterioration in your speech or voice?" id="voice" class="form-control" name="voice" autofocus>
                                </div>

                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Email" id="email" class="form-control" name="email" value="{{ old('email') }}"
                                           required autofocus email>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" placeholder="Password" id="password" class="form-control"
                                           name="password" required minlength="8">
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </form>

                            @if(!$errors->any())
                                <div class="row p-5">
                                    <div class="col-4">
                                        <button data-bs-target="#carouselEOPD" data-bs-slide="previous" class="btn btn-warning btn-lg form-control" role="button">Go Back</button>
                                    </div>
{{--                                    <div class="col-4">--}}
{{--                                        <a href="https://eopd.ie/" class="btn btn-warning btn-lg form-control" role="button">Go Back</a>--}}
{{--                                    </div>--}}
                                    <div class="col-4 offset-4">
                                        <button class="btn btn-success btn-lg form-control form-validator" role="button">Next</button>
                                    </div>
                                </div>
                            @else
                                <div class="row p-5">
                                    <div class="col-4">
                                        <button class="btn btn-warning btn-lg form-control" role="button" onclick="window.history.back()">DECLINE</button>
{{--                                        <a href="https://eopd.ie/" class="btn btn-warning btn-lg form-control" role="button">DECLINE</a>--}}
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('login') }}" class="btn btn-success btn-lg form-control" role="button">Log In</a>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-success btn-lg form-control form-submit">ACCEPT & SUBMIT</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="carousel-item">
                            <h1 class="text-center">EOPD Voice Biometric Study</h1>
                            <h6 class="text-center mb-5">INFORMATION SHEET #1 (continued)</h6>

                            <h3>Dear Participant,</h3>
                            <p>We are undertaking a MSc in Digital Health Transformation at the University of Limerick under the supervision of Annette McElligott. The title of our proposed research is: <u>“A protocol for data
                                    collection involving
                                    voice and speech biomarkers to assist in determining the progression of Parkinson’s Disease: an exploratory study.”</u></p>
                            <h3>Purpose of the Study:</h3>
                            <p>Our study will examine the measurement of voice and speech biomarkers to help track the progression of Parkinson’s Disease. As it is well known that Parkinson's disease affects the voice, we
                                believe it would be useful to
                                develop a practical means for gathering voice samples from people with Parkinson's for analysis by speech therapists as well as Parkinson's specialists. There are many areas to consider, not least
                                of all consent and
                                privacy protection, but also audio quality and what vocal exercises will yield the best results. The purpose of this project is to see if effective monitoring of speech and voice can be conducted
                                by individuals with
                                Parkinson’s Disease in their own homes.</p>
                            <h3>What will the study involve?</h3>
                            <p>This research study will involve individuals with Parkinson’s Disease audio recording themselves reading a passage of text and completing specific speech tasks from home. You will be asked to
                                complete these tasks first
                                thing in the morning, and also several hours later, which will take less than 10 minutes each time. You will be asked to complete this twice within 6 weeks.</p>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="previous" class="btn btn-warning btn-lg form-control" role="button">Go Back</button>
                                </div>
                                <div class="col-4 offset-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="next" class="btn btn-success btn-lg form-control" role="button">Next</button>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <h1 class="text-center">EOPD Voice Biometric Study</h1>
                            <h6 class="text-center mb-5">INFORMATION SHEET #1 (continued)</h6>
                            <h3>Why have you been asked to take part?</h3>
                            <p>You have been asked to participate because you are an individual with a diagnosis of Parkinson’s Disease. We also are looking for participants without a diagnosis of Parkinson’s Disease to take
                                part as the control
                                group</p>
                            <h3>Do you have to take part?</h3>
                            <p>No, you are under no obligation to participate in this research study. After reading this Information Sheet you will be asked to sign a Consent Form. You have the right to withdraw from this study
                                at any stage in the
                                process or up to a period of two weeks after the data collection is completed.</p>
                            <h3>Will your participation in this study be kept anonymous?</h3>
                            <p>Yes, your identity will be kept anonymous and no clues to your identity will appear in our project.</p>
                            <h3>What will happen to the information which you give?</h3>
                            <p>All data collected for this research study will be kept confidential for the duration of the study, only available to us and our research supervisor. It will be stored on a memory stick which will
                                only be accessible by
                                passcode. They will be retained for a minimum of seven years and then destroyed.</p>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="previous" class="btn btn-warning btn-lg form-control" role="button">Go Back</button>
                                </div>
                                <div class="col-4 offset-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="next" class="btn btn-success btn-lg form-control" role="button">Next</button>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <h1 class="text-center">EOPD Voice Biometric Study</h1>
                            <h6 class="text-center mb-5">INFORMATION SHEET #1 (continued)</h6>
                            <h3>What will happen to the results?</h3>
                            <p>The results will be presented in our project. They will be seen by our supervisor, a second marker and the external examiner. The study may be published.</p>
                            <h3>What are the possible disadvantages of taking part?</h3>
                            <p>We don’t expect any negative consequences to occur during the participation of this study. However, if you feel distressed or uncomfortable while discussing your experiences, please contact us (our
                                contact details appear
                                below)</p>
                            <h3>What if there is a problem?</h3>
                            <p>If you have any difficulties with the technology for this protocol, we are available by phone to help. If you feel in any way distressed, please note there are support services available such as
                                Parkinson's support
                                groups, your GP and the Parkinson’s Association of Ireland.</p>
                            <h3>Who has reviewed this study?</h3>
                            <p>Prior to the introduction of this study, this study has sought approval from the Faculty of Science & Engineering Research Ethics Committee in the University of Limerick.</p>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="previous" class="btn btn-warning btn-lg form-control" role="button">Go Back</button>
                                </div>
                                <div class="col-4 offset-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="next" class="btn btn-success btn-lg form-control" role="button">Next</button>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <h1 class="text-center">EOPD Voice Biometric Study</h1>
                            <h6 class="text-center mb-5">INFORMATION SHEET #1 (continued)</h6>

                            <h3>Any further queries?</h3>
                            <p>If you need any further information, you can contact us:</p>
                            <ul>
                                <li class="p-2">Isobel Cuddigan, (086) 8247248, <a href="mailto:21370745@studentmail.ul.ie">21370745@studentmail.ul.ie</a></li>
                                <li class="p-2">JP Swaine, (087) 2791398, <a href="mailto:21370737@studentmail.ul.ie">21370737@studentmail.ul.ie</a></li>
                                <li class="p-2">Gavin O’Duffy (087) 3647251, <a href="mailto:21381372@studentmail.ul.ie">21381372@studentmail.ul.ie</a></li>
                            </ul>
                            <p>Our supervisor can be contacted via email at <a href="mailto:annette.mcelligott@ul.ie">annette.mcelligott@ul.ie</a></p>

                            <b>If you have concerns about this study and wish to contact someone independent, you may contact:</b>
                            <div>The Chair,</div>
                            <div>Faculty of Science & Engineering Research Ethics Committee,</div>
                            <div>University of Limerick,</div>
                            <div>Limerick.</div>
                            <div>Tel: 061 237719</div>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button class="btn btn-warning btn-lg form-control" role="button" onclick="window.history.back()">Go Back</button>
{{--                                    <a href="https://eopd.ie/" class="btn btn-warning btn-lg form-control" role="button">Go Back</a>--}}
                                </div>
                                <div class="col-4 offset-4">
                                    <button data-bs-target="#carouselEOPD" data-bs-slide="next" class="btn btn-success btn-lg form-control" role="button">Next</button>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <h2 class="text-center">UNIVERSITY OF LIMERICK FACULTY OF SCIENCE & ENGINEERING</h2>
                            <h2 class="text-center mb-5">RESEARCH ETHICS COMMITTEE ETHICAL CONSENT FORM</h2>

                            <p>I declare that I am willing to take part in research for the project entitled <i><b>“A protocol for data collection involving voice and speech biomarkers to
                                        assist in determining the progression of Parkinson’s Disease: an exploratory study.”</b></i></p>
                            <ul>
                                <li class="p-2">I declare that I have been fully briefed on the nature of this study and my role in it and have been given the opportunity to ask questions before agreeing to participate.</li>
                                <li class="p-2">The nature of my participation has been explained to me, and I have full knowledge of how the information collected will be used.</li>
                                <li class="p-2">I am aware that my participation in this study will be audio recorded and I agree to this. However, should I feel uncomfortable at any time, I can stop recording.</li>
                                <li class="p-2">I am aware that such information may also be used in future academic presentations and publications about this study.</li>
                                <li class="p-2">I fully understand that there is no obligation on me to participate in this study.</li>
                                <li class="p-2">I fully understand that I am free to withdraw my participation without having to explain or give a reason, up to a period of two weeks after the data collection is completed.</li>
                                <li class="p-2">I acknowledge that the researchers guarantee that they will not use my name or any other information, that would identify me in any outputs of the research.</li>
                                <li class="p-2">I declare that I have read and fully understand the contents of the Research Privacy Notice.</li>
                            </ul>
                            <div class="row p-5">
                                <div class="col-4">
                                    <button class="btn btn-warning btn-lg form-control" role="button" onclick="window.history.back()">DECLINE</button>
{{--                                    <a href="https://eopd.ie/" class="btn btn-warning btn-lg form-control" role="button">DECLINE</a>--}}
                                </div>
                                <div class="col-4 offset-4">
                                    <button type="submit" class="btn btn-success btn-lg form-control form-submit">ACCEPT & SUBMIT</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            'use strict'

            const myCarouselElement = document.querySelector('#carouselEOPD');
            const carousel = new bootstrap.Carousel(myCarouselElement, {
                interval: false,
                wrap: false
            })

            var form = document.querySelectorAll('form')[0];
            var formValidators = document.querySelectorAll('.form-validator');
            var formSubmits = document.querySelectorAll('.form-submit');

            formValidators.forEach(formValidator => {
                formValidator.addEventListener('click', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    } else {
                        carousel.next();
                    }
                    form.classList.add('was-validated')
                }, false);
            });
            formSubmits.forEach(formSubmit => {
                formSubmit.addEventListener('click', function (event) {
                    form.submit();
                }, false);
            });

        })()
    </script>
@endsection
