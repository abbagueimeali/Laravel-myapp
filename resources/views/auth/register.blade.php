@extends('base')

@section('title', 'Register')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-5 mx-auto">
                <h1 class="text-center text-muted mb-3 mt-5">Register</h1>
                <p class="text-center text-muted mb-5">Create in account if you don't have one.</p>

                <form method="POST" action="{{ route('register') }}" class="row g-3" id="form-register">
                    @csrf

                    <div class="col-md-6">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>
                        <small class="text-danger fw-bold" id="error-register-firstname"></small>
                    </div>

                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname') }}" required autocomplete="lastname" autofocus>
                        <small class="text-danger fw-bold" id="error-register-lastname"></small>
                    </div>

                    <div class="col-md-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" url-emailExist="{{ route('app_exist_email') }}" token="{{ csrf_token() }}">
                        <small class="text-danger fw-bold" id="error-register-email"></small>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" required autocomplete="password">
                        <small class="text-danger fw-bold" id="error-register-password"></small>
                    </div>

                    <div class="col-md-6">
                        <label for="password-confirm" class="form-label">Password Confirmation</label>
                        <input type="password" class="form-control" id="password-confirm" name="password-confirm" value="{{ old('password-confirm') }}" required autocomplete="password-confirm">
                        <small class="text-danger fw-bold" id="error-register-password-confirm"></small>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="agreeTerms">
                            <label class="form-check-label" for="agreeTerms">Agree Terms</label><br>
                            <small class="text-danger fw-bold" id="error-register-agreeTerms"></small>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="button" id="register-user">Register</button>
                    </div>

                    <p class="text-center text-muted mt-5">Already have an account ? <a href="{{ route('login') }}">Login</a></p>

                </form>
            </div>
        </div>
    </div>
@endsection
