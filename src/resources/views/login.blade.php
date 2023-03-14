@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="text-center">EOPD Voice Biometric Study</h1>
                <h2 class="text-center mb-5">Log In</h2>

                <form action="{{ route('postLogin') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="text" placeholder="Email" id="email" class="form-control" name="email"
                               required autofocus email>
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" placeholder="Password" id="password" class="form-control"
                               name="password" required>
                        @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    @if(!$errors->any())
                        <div class="row p-5">
                            <div class="col-4">
                                <button class="btn btn-warning btn-lg form-control" role="button" onclick="window.history.back()">Go Back</button>
{{--                                <a href="https://eopd.ie/" class="btn btn-warning btn-lg form-control" role="button">Go Back</a>--}}
                            </div>
                            <div class="col-4 offset-4">
                                <input type="submit" class="btn btn-success btn-lg form-control form-validator" role="button" value="Log In"/>
                            </div>
                        </div>
                    @else
                        <div class="row p-5">
                            <div class="col-4">
                                <button class="btn btn-warning btn-lg form-control" role="button" onclick="window.history.back()">Go Back</button>
{{--                                <a href="https://eopd.ie/" class="btn btn-warning btn-lg form-control" role="button">Go Back</a>--}}
                            </div>
                            <div class="col-4">
                                <a href="{{ route('login') }}" class="btn btn-success btn-lg form-control" role="button">Reset password</a>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-success btn-lg form-control form-validator" role="button">Log In</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
