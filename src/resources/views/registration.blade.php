@extends('app')
@section('content')
    <main class="signup-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card">
                        <h3 class="card-header text-center">Register User</h3>
                        <div class="card-body">
                            <form action="{{ route('registering') }}" method="POST">
                                @csrf
                                <p>THIS WILL NEED TO IMPLEMENTED USING AN ONLINE FORM, WHICH WILL BE STORED WITH THE CONSENT FORM</p>
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Age" id="age" class="form-control" name="age" autofocus>
                                </div>
                                <div class="form-group mb-3">
                                    <select placeholder="Sex" id="sex" class="form-control" name="sex" autofocus>
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
                                    <select placeholder="Sex" id="sex" class="form-control" name="sex" autofocus>
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
                                    <input type="text" placeholder="Email" id="email" class="form-control" name="email"
                                           required autofocus>
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
                                <div class="form-group mb-3">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="remember"> Remember Me</label>
                                    </div>
                                </div>
                                <div class="d-grid mx-auto text-right">
                                    <button type="submit" class="btn btn-success">Sign up</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
