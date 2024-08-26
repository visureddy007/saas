@extends('layouts.app', ['class' => 'main-content-has-bg'])

@section('content')
    @include('layouts.headers.guest')

    <div class="container lw-guest-page-container-block pb-2">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <div class="mb-4 text-sm text-gray-600">
                            {{ __tr('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                        </div>

                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ __tr('A new verification link has been sent to the email address you provided during registration.') }}
                            </div>
                        @endif

                        <div class="mt-4 flex items-center justify-between text-center">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <div>
                                    <x-button class="btn btn-primary btn-lg">
                                        {{ __tr('Resend Verification Email') }}
                                    </x-button>
                                </div>
                            </form>
                            <a data-method="post" href="{{ route('auth.logout') }}" class="btn btn-default btn-sm lw-ajax-link-action mt-4">
                                <span>{{ __tr('Logout') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
