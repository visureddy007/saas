@extends('layouts.app', ['class' => 'main-content-has-bg'])

@section('content')
@include('layouts.headers.guest')

<div class="container lw-guest-page-container-block pb-2">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow border-0">
                <div class="card-body px-lg-5 py-lg-5">
                    <div class="mb-4 text-sm text-gray-600">
                        <h2>{{  __tr('Forgot your password?') }}</h2>
                        <hr>
                        {{ __tr('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </div>
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <x-lw.form action="{{ route('auth.password.request') }}" class="lw-ajax-form" data-secured="true">
                        <!-- Email Address -->
                        <div>
                            <x-lw.input-field :label="__tr('Email')" id="email" type="email" name="email" placeholder="{{ __tr('Email') }}" required autofocus>
                                <x-slot name="prepend">
                                    <span class="input-group-text"><i class="fa fa-at"></i></span>
                                </x-slot>
                            </x-lw.input-field>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-lw.button>
                                {{ __tr('Email Password Reset Link') }}
                                </x-w.button>
                        </div>
                    </x-lw.form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection