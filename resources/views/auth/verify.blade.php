@extends('layouts.app', ['class' => 'main-content-has-bg'])

@section('content')
@include('layouts.headers.guest')

<div class="container lw-guest-page-container-block pb-2">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow border-0">
                <div class="card-body px-lg-5 py-lg-5">
                    <div class="text-center text-muted mb-4">
                        <small>{{ __tr('Verify Your Email Address') }}</small>
                    </div>
                    <div>
                        @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __tr('A fresh verification link has been sent to your email address.') }}
                        </div>
                        @endif

                        {{ __tr('Before proceeding, please check your email for a verification link.') }}

                        @if (Route::has('verification.resend'))
                        {{ __tr('If you did not receive the email') }}, <a href="{{ route('verification.resend') }}">{{ __tr('click here to request another') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection