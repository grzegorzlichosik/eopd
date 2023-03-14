<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EOPD Voice Biometric Study</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <style>
        audio{
            width: 100%;
        }
        :root {
            --bs-body-color: #d4d8db;
            --bs-body-color-rgb: 212, 216, 219;

            --bs-body-bg: #3156A6;
            --bs-body-bg-rgb: 49, 86, 166;

            /*--bs-body-bg: #0099e8;*/
            /*--bs-body-bg-rgb: 0, 153, 232;*/


            --bs-border-color: #d7dde2;
            --bs-border-color-translucent: rgba(255, 255, 255, 0.15);


            --bs-emphasis-color: #f8f9fa;
            --bs-emphasis-color-rgb: 248,249,250;
            --bs-secondary-color: rgba(173, 181, 189, 0.75);
            --bs-secondary-color-rgb: 173,181,189;
            --bs-secondary-bg: #343a40;
            --bs-secondary-bg-rgb: 52,58,64;
            --bs-tertiary-color: rgba(173, 181, 189, 0.5);
            --bs-tertiary-color-rgb: 173,181,189;
            --bs-tertiary-bg: #2b3035;
            --bs-tertiary-bg-rgb: 43,48,53;
            --bs-emphasis-color: #fff;
            --bs-primary-text: #6ea8fe;
            --bs-secondary-text: #dee2e6;
            --bs-success-text: #75b798;
            --bs-info-text: #6edff6;
            --bs-warning-text: #ffda6a;
            --bs-danger-text: #ea868f;
            --bs-light-text: #f8f9fa;
            --bs-dark-text: #dee2e6;
            --bs-primary-bg-subtle: #031633;
            --bs-secondary-bg-subtle: #212529;
            --bs-success-bg-subtle: #051b11;
            --bs-info-bg-subtle: #032830;
            --bs-warning-bg-subtle: #332701;
            --bs-danger-bg-subtle: #2c0b0e;
            --bs-light-bg-subtle: #343a40;
            --bs-dark-bg-subtle: #1a1d20;
            --bs-primary-border-subtle: #084298;
            --bs-secondary-border-subtle: #495057;
            --bs-success-border-subtle: #0f5132;
            --bs-info-border-subtle: #055160;
            --bs-warning-border-subtle: #664d03;
            --bs-danger-border-subtle: #842029;
            --bs-light-border-subtle: #495057;
            --bs-dark-border-subtle: #343a40;
            --bs-heading-color: #fff;
            --bs-link-color: #6ea8fe;
            --bs-link-hover-color: #9ec5fe;
            --bs-link-color-rgb: 110,168,254;
            --bs-link-hover-color-rgb: 158,197,254;
            --bs-code-color: #e685b5;
        }

    </style>
</head>
<body class="p-5">

<div class="container mb-5">
    <div class="row">
        <div class="col-6">
            <img class="img-fluid" src="/img/eopd.png">
        </div>
        <div class="col-6" style="text-align: right;">
            <img src="/img/uni.png">
        </div>
    </div>
</div>

@guest
@else
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="alert alert-primary" role="alert">
                    <a href="{{ route('signout') }}" class="alert-link">Logout ({{Auth::user()->email}})</a>
                </div>
            </div>
        </div>
    </div>
@endguest
@if(session()->has('success'))
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session()->get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
@endif
@if(session()->has('error'))
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session()->get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
@endif
@yield('content')

</body>
</html>
