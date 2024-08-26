<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="<?= config('CURRENT_LOCALE_DIRECTION') ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Primary Meta Tags -->
    <title>@yield('title')</title>
    <meta name="title" content="@yield('title')">
    <meta name="description" content="@yield('title')">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('title')">
    <meta property="og:image" content="{{ getAppSettings('logo_image_url') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="@yield('title')">
    <meta property="twitter:description" content="@yield('title')">
    <meta property="twitter:image" content="{{ getAppSettings('logo_image_url') }}">

    <?= __yesset([
    'static-assets/packages/fontawesome/css/all.css',
    'dist/css/vendorlibs.css',
    'argon/css/argon.min.css',
    'dist/css/app.css'
        ], true) ?>

    <link rel="shortcut icon" href="<?= getAppSettings('favicon_image_url') ?>" type="image/x-icon">
    <link rel="icon" href="<?= getAppSettings('favicon_image_url') ?>" type="image/x-icon">
</head>

<body id="page-top" class="lw-gradient-bg">

    <!-- Page Wrapper -->
    <!-- Begin Page Content -->
    <div class="lw-page-content lw-other-page-content">
        <section class="section ">
            <div class="container text-center">
                <div class="row">
                    <div class="col-12 my-1">
                        <img class="lw-logo-img mt-5" src="<?= getAppSettings('logo_image_url') ?>"
                            alt="<?= getAppSettings('name') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-8 offset-2  mb-5 lw-error-page-block">
                        <i class="fas fa-exclamation-triangle fa-10x text-danger"></i>
                        <h1 class="fa-8x">@yield('code')</h1>
                            <h2 class="text-muted"> @yield('title')</h2>
                        <p class="my-5 fa-2x">@yield('message')</p>
                      <div class="mb-6">
                        <a href="{{ url('') }}" class="btn btn-primary btn-lg mt-5">{{ __tr('Back to Home') }}</a>
                      </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>