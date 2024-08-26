@extends('layouts.app', ['class' => 'main-content-has-bg'])
@section('content')
@include('layouts.headers.guest')
<div class="container lw-guest-page-container-block pb-2">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card lw-form-card-box shadow border-0">
                <h1 class="card-header text-center">
                    <div class="my-2">
                        <i class="fa fa-lock text-gray"></i>  {{  __tr('Account Access') }}
                    </div>
                </h1>
                @if (isDemo())
                    <div class="card-header text-center">
                        <button onclick="document.getElementById('lwLoginEmail').value='demosuperadmin';document.getElementById('lwLoginPassword').value='demopass12';" class="btn btn-sm btn-danger">{{  __tr('Demo Super Admin Login') }}</button>
                        <button onclick="document.getElementById('lwLoginEmail').value='testcompany';document.getElementById('lwLoginPassword').value='demopass12';" class="btn btn-sm btn-danger">{{  __tr('Demo Company Login') }}</button>
                    </div>
                @endif
                <div class="card-body px-lg-5 py-lg-5">
                    <x-lw.form id="lwLoginForm" data-secured="true" :action="route('auth.login.process')">
                        <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }} mb-3">
                            <div class="input-group input-group-alternative">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-user-alt"></i></span>
                                </div>
                                <input id="lwLoginEmail" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __tr('Email or Username or Mobile Number') }}" type="text" name="email" value="" required autofocus autocomplete="email">
                            </div>
                            <h5><span class="text-light">{{__tr("Mobile number should be with country code without 0 or +")}}</span></h5>
                        
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                </div>
                                <input id="lwLoginPassword" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __tr('Password') }}" type="password" value="" required autocomplete="current-password">
                            </div>
                        </div>
                        <div class="custom-control custom-control-alternative custom-checkbox">
                            <input class="custom-control-input" name="remember" id="customCheckLogin" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customCheckLogin">
                                <span class="text-light">{{ __tr('Remember me') }}</span>
                            </label>
                            @if (Route::has('auth.password.request'))
                            <a href="{{ route('auth.password.request') }}" class="text-light float-right">
                                <small>{{ __tr('Forgot password?') }}</small>
                            </a>
                            @endif
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success my-4 btn-lg btn-block mb-5">{{ __tr('Login') }}</button>
                        </div>
                    </x-lw.form>
                </div>
                <div class="card-footer text-center">
                    <!-- social login links -->
                    @if(getAppSettings('allow_google_login'))
                    <a href="<?= route('login.google') ?>" class="btn btn-google btn-user btn-block">
                        <i class="fab fa-google fa-fw"></i> <?= __tr('Continue with Google')  ?>
                    </a>
                    @endif
                    @if(getAppSettings('allow_facebook_login'))
                    <a href="<?= route('login.facebook') ?>" class="btn btn-facebook btn-user btn-block">
                        <i class="fab fa-facebook-f fa-fw"></i> <?= __tr('Continue with Facebook')  ?>
                    </a>
                    @endif
                    @if(getAppSettings('enable_vendor_registration'))
                    <!-- social login links -->
                    <div class="mb-3 mt-5">
                        {{  __tr('If you don\'t have an Account yet? Create One! Its Free!!') }}
                    </div>
                    <a href="{{ route('auth.register') }}" class="btn btn-lg btn-warning">
                        <small>{{ __tr('Create New Account') }}</small>
                    </a>
                    @elseif(getAppSettings('message_for_disabled_registration'))
                    <div class="mb-3 mt-5">
                        {{  __tr('Want to create New Account?') }}
                    </div>
                    <a href="{{ route('auth.register') }}" class="btn btn-lg btn-warning">
                        <small>{{ __tr('More Info') }}</small>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection