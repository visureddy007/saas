<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $CURRENT_LOCALE_DIRECTION ?? ''}}">
@php
$vendorSlug = getPublicVendorSlug();
$vendorId = getPublicVendorId();
$vendorUid = getPublicVendorUid();
@endphp
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{ (isset($title) and $title) ? ' - ' . $title : __tr('Welcome') }} - {{ getAppSettings('name') }}</title>
    <!-- Favicon -->
    <link href="{{ getAppSettings('favicon_image_url') }}" rel="icon">
    {!! __yesset([
        'static-assets/packages/bootstrap-icons/font/bootstrap-icons.css'
    ]) !!}
    <!-- Google fonts-->
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,600;1,600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300;0,500;0,600;0,700;1,300;1,500;1,600;1,700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,400;1,400&amp;display=swap"
        rel="stylesheet" />
    @stack('head')
    {!! __yesset(
    [
    'dist/css/common-vendorlibs.css',
    'dist/css/app-public.css',
    ],
    true,
    ) !!}
    {{-- custom app css --}}

</head>

<body class="{{ $class ?? '' }} pb-6">

    <div class="main-content text-center p-5">
        <h1 class="mt-5">{{ getVendorSettings('title') }}</h1>
        <div class="card col-sm-12 col-md-6 offset-md-3 mt-5 bg-secondary text-white">
            <div class="card-body">
                @if (isVendorAdmin($vendorId))
                <h2 class="text-warning">{!! __tr("You don't have any active plan") !!}</h2>
                <p>{{  __tr('Please go to your vendor account and get one : ') }} <strong><a href="{{ route('subscription.read.show') }}">{{ __tr('Subscribe') }}</a></strong></p>
                @else
                <h2 class="text-warning">{{  __tr('Store is Down Now') }}</h2>
                <p>{{  __tr('Please contact store owner at : ') }} <strong>{{ getVendorSettings('contact_email') }}</strong></p>
                @endif
            </div>
        </div>
    </div>

</body>
</html>