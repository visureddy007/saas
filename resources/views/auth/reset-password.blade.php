@extends('layouts.app', ['class' => 'main-content-has-bg'])

@section('content')
@include('layouts.headers.guest')

<div class="container lw-guest-page-container-block pb-2">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow border-0">
                <div class="card-body px-lg-5 py-lg-5">
                    <div class="mb-4 text-sm text-gray-600">
                        <h2>{{  __tr('Reset your password') }}</h2>
                        <hr>
                        {{  __tr('Please choose new password and confirm it.') }}
                    </div>
                    <!-- Validation Errors -->
                    <x-lw.form action="{{ route('auth.password.reset.process') }}" class="lw-ajax-form" data-secured="true">
                         <!-- Password Reset Token -->
                        <x-lw.input type="hidden" name="token" value="{{ $request->route('token') }}" />
                        <!-- Email Address -->
                        <x-lw.input-field :label="__tr('Email')" id="email" type="email" name="email" placeholder="{{ __tr('Email') }}" required autofocus value="{{ request('email') }}" >
                            <x-slot name="prepend">
                                <span class="input-group-text"><i class="fa fa-at"></i></span>
                            </x-slot>
                        </x-lw.input-field>
                        <!-- Password -->
                        <x-lw.input-field :label="__tr('Password')" placeholder="{{ __tr('New Password') }}" id="password" type="password" name="password" required autocomplete="current-password" >
                            <x-slot name="prepend">
                                <span class="input-group-text"><i class="fa fa-key"></i></span>
                            </x-slot>
                        </x-lw.input-field>
                        <!-- Confirm Password -->
                        <x-lw.input-field :label="__tr('Confirm Password')" id="confirmPassword" placeholder="{{ __tr('Confirm New Password') }}" type="password" name="password_confirmation" required >
                            <x-slot name="prepend">
                                <span class="input-group-text"><i class="fa fa-key"></i> <sup><i class="fa fa-check"></i></sup></span>
                            </x-slot>
                        </x-lw.input-field>
                        <div class="flex justify-end mt-4">
                            <x-lw.button >
                                {{ __tr('Confirm') }}
                            </x-lw.button>
                        </div>
                    </x-lw.form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection