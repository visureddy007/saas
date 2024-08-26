@extends('layouts.app', ['title' => __tr('User Profile')])

@section('content')
@include('users.partials.header', [
'title' => __tr('Your Profile') . ' '. auth()->user()->name,
'description' => '',
'class' => 'col-lg-7'
])

<div class="container-fluid mt-lg--1">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card shadow col-xl-6">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <h1 class="mb-0">{{ __tr('Edit Profile') }}</h1>
                    </div>
                </div>
                <div class="card-body">
                    <x-lw.form :action="route('user.profile.update')">
                        <h3 class="text-muted">{{ __tr('User information') }}</h3>
                        <hr class="my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="lwFirstName">{{ __tr('First Name') }}</label>
                                    <input type="text" name="first_name" id="lwFirstName"
                                        class="form-control form-control-alternative{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __tr('First Name') }}"
                                        value="{{ old('first_name', auth()->user()->first_name) }}" required autofocus>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="lwLastName">{{ __tr('Last Name') }}</label>
                                    <input type="text" name="last_name" id="lwLastName"
                                        class="form-control form-control-alternative{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __tr('Last Name') }}"
                                        value="{{ old('last_name', auth()->user()->last_name) }}" required>
                                </div>
                            </div>
                        </div>
                         {{-- MOBILE NUMBER --}}
                         <div class="">
                            <div class="form-group">
                                <label class="form-control-label" for="input-mobile-number">{{ __tr('Mobile Number') }}</label>
                                <input class="form-control form-control-alternative{{ $errors->has('mobile_number') ? ' is-invalid' : '' }}" placeholder="{{ __tr('Mobile Number') }}" type="number" name="mobile_number" value="{{ old('mobile_number', auth()->user()->mobile_number) }}" required >
                            </div>
                        </div>
                        <h5> <span class="text-muted">{{__tr("Mobile number should be with country code without 0 or +")}}</span></h5>
               
                {{-- /MOBILE NUMBER --}}
                        <div class="">
                            <div class="form-group">
                                <label class="form-control-label" for="input-email">{{ __tr('Email') }}</label>
                                <input type="email" name="email" id="input-email"
                                    class="form-control form-control-alternative{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __tr('Email') }}" value="{{ old('email', auth()->user()->email) }}"
                                    required>
                            </div>
                            <div class="lw-form-footer">
                                <button type="submit" class="btn btn-primary mt-4">{{ __tr('Save') }}</button>
                            </div>
                        </div>
                    </x-lw.form>
                </div>

            </div>
            <div class="card bg-secondary col-xl-6 shadow mt-4">
                <div class="card-body">
                    <x-lw.form class="" data-secured="true" method="post"
                        action="{{ route('auth.password.update.process') }}" autocomplete="off">

                        <h3 class="text-muted">{{ __tr('Password') }}</h3>
                        <hr class="my-3">
                        @if (session('password_status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('password_status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <div class="">
                            <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="input-current-password">{{ __tr('Current Password')
                                    }}</label>
                                <input type="password" name="old_password" id="input-current-password"
                                    class="form-control form-control-alternative{{ $errors->has('old_password') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __tr('Current Password') }}" value="" required>

                                @if ($errors->has('old_password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('old_password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="input-password">{{ __tr('New Password') }}</label>
                                <input type="password" name="password" id="input-password"
                                    class="form-control form-control-alternative{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __tr('New Password') }}" value="" required>

                                @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="input-password-confirmation">{{ __tr('Confirm New
                                    Password') }}</label>
                                <input type="password" name="password_confirmation" id="input-password-confirmation"
                                    class="form-control form-control-alternative"
                                    placeholder="{{ __tr('Confirm New Password') }}" value="" required>
                            </div>
                            <div class="lw-form-footer">
                                <button type="submit" class="btn btn-primary mt-4">{{ __tr('Change password') }}</button>
                            </div>
                        </div>
                    </x-lw.form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection