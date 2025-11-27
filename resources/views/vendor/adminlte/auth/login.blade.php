@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passwordResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
        $passwordResetUrl = $passwordResetUrl ? route($passwordResetUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
        $passwordResetUrl = $passwordResetUrl ? url($passwordResetUrl) : '';
    }
@endphp

@section('auth_header', 'Iniciar sesión')

@section('auth_body')
    <form action="{{ $loginUrl }}" method="post">
        @csrf

        {{-- Campo usuario o correo --}}
        <div class="input-group mb-3">
            <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                value="{{ old('login') }}" placeholder="Usuario o correo" required autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>

            @error('login')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Campo contraseña --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Contraseña" required>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>


            <div class="col-5">
                <button type="submit" class="btn btn-dark btn-block">
                    <i class="fas fa-sign-in-alt"></i> Ingresar
                </button>
            </div>
        </div>
    </form>
@stop

