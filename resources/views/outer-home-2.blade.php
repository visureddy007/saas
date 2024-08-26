<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $CURRENT_LOCALE_DIRECTION ?? '' }}">
@php
$appName = getAppSettings('name');
@endphp
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{ (isset($title) and $title) ? ' - ' . $title : __tr('Welcome') }} - {{ $appName }}</title>
    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $appName }}" />
    <meta name="description" content="{{ getAppSettings('description') }}" />
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $appName }}" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:title" content="{{ $appName }}" />
    <meta property="og:description" content="{{ getAppSettings('description') }}" />
    <meta property="og:image" content="{{ getAppSettings('logo_image_url') }}" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url('/') }}" />
    <meta property="twitter:title" content="{{ $appName }}" />
    <meta property="twitter:description" content="{{ getAppSettings('description') }}" />
    <meta property="twitter:image" content="{{ getAppSettings('logo_image_url') }}" />

    <!-- FAVICON -->
    <link href="{{ getAppSettings('favicon_image_url') }}" rel="icon">
    {!! __yesset([
    'static-assets/packages/fontawesome/css/all.css',
    'static-assets/packages/bootstrap-icons/font/bootstrap-icons.css',
    ]) !!}
    <!-- Google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
        rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- /Google fonts-->

<body class="lw-outer-home-page">
    {!! __yesset(['dist/css/app-home.css'], true) !!}
    `

    <body id="page-top">
        <!-- Navigation-->
        <header class="lw-top-navbar">
            <nav class="navbar navbar-expand-lg navbar-light fixed-top border-bottom" id="mainNav">
                <div class="container px-5">
                    <!-- Logo -->
                    <!-- Brand -->
                    <a class="navbar-brand pt-0" href="">
                        <img src="{{ getAppSettings('logo_image_url') }}" class="navbar-brand-img" alt="">
                    </a>
                    <!-- Logo -->
                    <button class="navbar-toggler lw-btn-block-mobile" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                        aria-label="{{ __tr('Toggle navigation') }}">
                        {{ __tr('Menu') }}
                        <i class="bi-list"></i>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarResponsive">
                        <ul class="navbar-nav ms-auto me-4 my-3 my-lg-0 text-center">
                            <!-- Menu -->
                            <!-- Features -->
                            <li class="nav-item"><a class="nav-link me-lg-3" href="#features">{{ __tr('Features') }}</a>
                            </li>
                            <!-- /Features -->
                            <!-- Pricing -->
                            <li class="nav-item"><a class="nav-link me-lg-3" href="#pricing">{{ __tr('Pricing') }}</a>
                            </li>
                            <!-- /Pricing -->
                            <!-- Contact -->
                            <li class="nav-item"><a class="nav-link me-lg-3"
                                    href="{{ route('user.contact.form') }}">{{ __tr('Contact') }}</a></li>
                            <!-- /Contact -->
                             <!-- pages -->
                             <li class="nav-item">
                                @include('layouts.navbars.navs.pages-menu-partial')
                             </li>
                            <!-- /pages -->
                               <!-- /pages -->
                            @if (!isLoggedIn())
                            @if (getAppSettings('enable_vendor_registration') or
                            getAppSettings('message_for_disabled_registration'))
                            <!-- Login -->
                            <li class="nav-item"><a class="nav-link me-lg-3 lw-login-btn"
                                    href="{{ route('auth.login') }}">{{ __tr('Login') }}</a></li>
                            <!-- /Login -->
                            @endif
                            <!-- Register -->
                            <li class="nav-item"><a class="nav-link me-lg-3 lw-register-btn"
                                    href="{{ route('auth.register') }}">{{ __tr('Register') }}</a></li>
                            <!-- /Register -->
                            @endif
                            <!-- Dashboard -->
                            @if (isLoggedIn())
                            <li class="nav-item"><a class="nav-link me-lg-3 text-danger fw-bold "
                                    href="{{ route('central.console') }}">{{ __tr('Dashboard') }}</a></li>
                            @endif
                            <!-- /Dashboard -->
                            @include('layouts.navbars.locale-menu')
                            <!-- /Menu -->
                        </ul>

                    </div>
                </div>
            </nav>
        </header>
        <!-- /Navigation -->
        <!-- masthead section -->
        <section class="lw-masthead">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-sm-12 col-md-12 col-lg-6 text-center">
                        <div class="lw-image-fluid mt-4"><img class="img-fluid w-100 "
                                src="{{ asset('imgs/outer-home/masthead.png') }}" alt="..." />
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <!-- heading -->
                        <div class="lw-title ubuntu-bold">
                            {{ __tr('Engage Your Customers on WhatsApp Like Never Before') }} <span
                                class="highlight">{!! __tr(' __appName__ ', ['__appName__' => $appName]) !!}</span>
                        </div>
                        <!-- heading -->
                        <!-- description -->
                        <div class="description">
                            {{ __tr(
                                'Unlock the full potential of customer engagement with __appName__  your comprehensive WhatsApp Marketing Platform.',
                                [
                                    '__appName__' => $appName,
                                ],
                            ) }}
                        </div>
                        <!-- /description -->
                        <!-- buttons -->
                        <div class="my-4">
                            <a href="{{ route('auth.login') }}" class="btn lw-bg-success">
                                {{ __tr('Get Started') }}
                            </a>
                            <a href="{{ route('auth.register') }}" class="btn lw-btn mx-2">
                                {{ __tr('Signup Now') }}
                            </a>
                        </div>
                        <!-- buttons -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /masthead section -->
        <!-- why choose section -->
        <section class="lw-section-block-content">
            <div class="container">
                <!-- heading -->
                <h2 class="text-dark ubuntu-bold text-center"> {{ __tr('Why Choose') }} <span class="lw-highlight">{!!
                        __tr(' __appName__?', ['__appName__' => $appName]) !!}</span></h2>
                <!-- /heading -->
                <div class="row">
                    <!-- First card -->
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                        <div class="card text-center border-0 p-5">
                            <i class="fas fa-trophy lw-icon"></i>
                            <h5>{{ __tr('Increased Engagement') }}</h5>
                            <p>{{ __tr("Connect directly with your customers in real-time on WhatsApp using $appName intuitive interface and seamless integration. Build lasting relationships that drive results.") }}
                            </p>
                        </div>
                    </div>
                    <!-- /First card -->
                    <!-- second card -->
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                        <div class="card text-center border-0 p-5">
                            <i class="fas fa-people-arrows lw-icon"></i>
                            <h5>{{ __tr('Higher Conversion Rates') }}</h5>
                            <p>{{ __tr("With $appName every chat is an opportunity. Transform casual chats into meaningful interactions that lead to increased conversions through targeted messaging and personalized campaigns.") }}
                            </p>
                        </div>
                    </div>
                    <!-- /second card -->
                    <!-- third card -->
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                        <div class="card text-center border-0 p-5">
                            <i class="fas fa-hands-helping lw-icon"></i>
                            <h5>{{ __tr('24/7 Customer Support') }}</h5>
                            <p> {{ __tr("Your customers deserve the best support, and $appName delivers with 24/7 automated responses that ensure you never miss a beat. Stay connected, stay responsive, and watch your business thrive.") }}
                            </p>
                        </div>
                    </div>
                    <!-- /third card -->
                </div>
            </div>
        </section>
        <!-- /why choose section -->
        <!--  Feature Section -->
        <section class="lw-features-block" id="features">
            <div class="container text-center">
                <!-- heading -->
                <h4>{{ __tr('Features') }}</h4>
                <h2 class="ubuntu-bold">{{ __tr('Tech Empowerment') }}</h2>
                <p class="mb-5">{{ __tr('Features that would make your life easier with WhatsApp Marketing') }}</p>
                <!-- /heading -->
                <div class="row px-2">
                    <!-- Embedded Signup -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-sign-in-alt"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Embedded Signup') }}</h5>
                            <p>{{ __tr('Onboard customers with ease with our integrated Embedded Signup system.') }}
                            </p>
                        </div>
                    </div>
                    <!-- /Embedded Signup -->
                    <!-- Template Management  -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-file-invoice"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Template Management ') }}</h5>
                            <p>{{ __tr('Handle templates directly within the application without requiring a visit to Meta for creating templates.') }}
                            </p>
                        </div>
                    </div>
                    <!-- /Template Management  -->
                    <!-- Multiple Phone Numbers  -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-phone-alt"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Multiple Phone Numbers ') }}</h5>
                            <p>{{ __tr('Supports multiple phone numbers for  same WhatsApp Business Account') }}
                            </p>
                        </div>
                    </div>
                    <!-- /Multiple Phone Numbers  -->
                    <!-- WhatsApp Chat -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fab fa-rocketchat"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('WhatsApp Chat') }}</h5>
                            <p> {{ __tr( "$appName chat feature replicates the native WhatsApp interface, guaranteeing users a seamless and familiar messaging experience.",
                            ) }}
                            </p>
                        </div>
                    </div>
                    <!-- /WhatsApp Chat -->
                    <!-- Bot Replies/ Chat Bot -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-robot"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Bot Replies/ Chat Bot') }}</h5>
                            <p>{{ __tr('Automate responses and engage customers 24/7 with intelligent bot replies through') }}
                            </p>
                        </div>
                    </div>
                    <!-- /Bot Replies/ Chat Bot -->
                    <!-- APIs -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-cogs"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr(' APIs to connect with other services') }}
                            </h5>
                            <p>{{ __tr('API’s enable seamless connection between different services, allowing data sharing and functionality integration.') }}
                            </p>
                        </div>
                    </div>
                    <!-- /APIs -->
                    <!-- Manage Contacts -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-user"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Manage Contacts') }}</h5>
                            <p>{{ __tr('Effortlessly import and export contacts using XLSX format for easy contacts transfer along with Add/Edit functionality on interface.') }}
                            </p>
                        </div>
                    </div>
                    <!-- /Manage Contacts -->
                    <!--  Realtime Updates -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-bolt"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr(' Realtime Updates') }}</h5>
                            <p>{{ __tr('Realtime message and campaign status updates to see your campaign or message performance.') }}
                            </p>
                        </div>
                    </div>
                    <!-- / Realtime Updates -->
                    <!-- Dashboard -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-tachometer-alt"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Dashboard') }}</h5>
                            <p>{{ __tr('To provide with instant visibility into the performance and status of their marketing campaigns.') }}
                            </p>
                        </div>
                    </div>
                    <!-- /Dashboard -->
                    <!-- Team Members/Agents -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-user"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Team Members/Agents') }}</h5>
                            <p>{{ __tr('Delegate work by creating users with various permissions.') }}</p>
                        </div>
                    </div>
                    <!-- / Team Members/Agents -->
                    <!-- Interactive/Button Messages for bot reply -->
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fab fa-rocketchat"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Interactive/Button Messages for bot Reply') }}
                            </h5>
                            <p>{!! __tr(' __appName__', ['__appName__' => $appName])
                                !!}{{ __tr(' Advanced interactive bots now provide smarter, more engaging replies, supporting images, documents, videos, audios and interactive buttons for enhanced user interaction.') }}
                            </p>
                        </div>
                    </div>
                    <!-- /Interactive/Button Messages for bot reply -->
                    {{-- Custom Fields --}}
                    <div class="col-sm-12 col-md-6 col-lg-4 co-xl-4 mb-4">
                        <div class="card border-0 p-5 h-100 ">
                            <i class="fas fa-bars"></i>
                            <h5 class="ubuntu-bold mt-3 mb-2">{{ __tr('Custom Fields') }}</h5>
                            <p>{{ __tr('Personalize your messages with user base information and custom fields tailored to your audience on') }}{!!
                                __tr(' __appName__.', ['__appName__' => $appName]) !!}
                            </p>
                        </div>
                    </div>
                    {{-- /Custom Fields --}}
                </div>
            </div>
        </section>
        <!-- / Feature Section -->
        <!-- heading -->
        <section class="lw-bg-success text-white text-center p-5">
            <h2 class="ubuntu-bold"> {{ __tr('Make Connecting Easy with') }}<span class="text-warning">{!! __tr('
                    __appName__', ['__appName__' => $appName],
                    ) !!}</span></h2>
            <h4 class="ubuntu-bold"> {{ __tr('Grow Your Brand, Delight Your') }} <span class="text-warning">
                    {{ __tr('Customers!') }}</span></h4>
        </section>
        <!-- /heading -->
        <!-- Advance-Feature-section -->
        <section class="lw-advance-feature-block">
            <div class="container">
                <!-- Campaign Management -->
                <div class="row align-items-center p-3 mb-4">
                    <div class="col-sm-12 col-md-6 col-lg-6 text-center">
                        <!-- image -->
                        <img class="img-fluid w-75 mb-4" src="{{ asset('imgs/outer-home/campaign.png') }}" />
                        <!-- image -->
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <h2 class="ubuntu-bold my-2">{{ __tr('Campaign Management') }}</h2>
                        <h6 class="my-2 text-danger">
                            {{ __tr('Streamline Campaign Management: Create, Schedule, and Reach Instantly!') }}</h6>

                        <p>{{ __tr('Effortlessly manage your campaigns with our intuitive campaign management feature. Create or schedule campaigns instantly for all contacts or specific groups, allowing for immediate reach or strategic timing. Maximize the impact of your marketing efforts and take control of your messaging with ease.') }}
                        </p>
                    </div>
                </div>
                <!-- /Campaign Management -->

                <!-- Integrated WhatsApp Chat -->
                <div class="row align-items-center p-3 lw-mobile-div-reverse mb-5">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <h2 class="ubuntu-bold my-2">{{ __tr('Integrated WhatsApp Chat') }}</h2>
                        <h6 class="my-2 text-danger">{{ __tr('Enhance Customer Engagement and Support') }}</h6>
                        <p>{{ __tr('The Integrated WhatsApp Chat feature in ') }}{!! __tr(' __appName__',
                            ['__appName__' => $appName])
                            !!}{{ __tr(" provides a seamless and familiar messaging experience by faithfully replicating the native WhatsApp interface. Users can navigate effortlessly, leveraging their existing familiarity with WhatsApp's layout and functions. This consistency enhances user comfort and efficiency, facilitating smooth communication.") }}
                        </p>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 text-center">
                        <!-- image -->
                        <img class="img-fluid w-75 mb-4" src="{{ asset('imgs/outer-home/whatsapp-chat.png') }}" />
                        <!-- image -->
                    </div>
                </div>
                <!-- /Integrated WhatsApp Chat -->
            </div>
            <!-- Bot Flow Builder -->
            <section class="lw-main-feature-block mb-5">
                <div class="container">
                    <div class="row align-items-center p-3 my-3 ">
                        <div class="col-sm-12 col-md-6 col-lg-6 text-center">
                            <!-- image -->
                             <div class="lw-image-container mb-4">
                                <div class="lw-card"><img src="{{ asset('imgs/outer-home/bot-flow2.png') }}" /></div>
                                <div class="lw-card"><img src="{{ asset('imgs/outer-home/bot-flow3.png') }}" /></div>
                                <div class="lw-card"><img src="{{ asset('imgs/outer-home/bot-flow1.png') }}" /></div>
                            </div>
                             <!-- image -->
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <h2 class="ubuntu-bold my-2 ">{{ __tr('Bot Flow Builder') }}</h2>
                            <h6 class="my-2 text-danger">{{ __tr('Simplify Bot Flow Conversion Building') }}</h6>
                            <p class="">
                                {{ __tr("Our Advance Bot Flow builder helps you to build bot conversions easily and effectively, Bot flow builder Simplifies setting  triggering points from one bot to other using links for the buttons and list row options.") }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /Bot Flow Builder -->
            <div class="container lw-bg-success">
                <!--  Reached to the Customers -->
                <div class="row align-items-center p-3 lw-mobile-div-reverse mb-4">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <h2 class="ubuntu-bold my-2 text-white">{{ __tr('Reached to the Customers') }}</h2>
                        <h6 class="ubuntu-bold my-2 text-warning">
                            {{ __tr('Do you want to make your chat bot interactions
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            more exciting and engaging?') }}
                        </h6>
                        <p class="text-white">
                            {{ __tr("Our Advance bot feature allows you to send images, videos, and documents as well as buttons through your chat bot. Instead of only using text, you can now impress your customers with visually pleasing images, informative videos, and useful documents. It's a great way to capture their attention and provide them with valuable content. Try now and take your chat bot interactions to the next level!") }}
                        </p>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 text-center">
                        <!-- image -->
                        <img class="img-fluid w-75 mb-4 rounded" src="{{ asset('imgs/outer-home/media-message.png') }}" />
                        <!-- image -->
                    </div>
                </div>
                <!-- /Reached to the Customers -->

                <!-- or code -->
                <div class="row align-items-center p-3 mb-4">
                    <div class="col-sm-12 col-md-6 col-lg-6 text-center">
                        <!-- image -->
                        <img class="img-fluid w-75 mb-4" src="{{ asset('imgs/outer-home/qr-code.png') }}" />
                        <!-- image -->
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <h2 class="ubuntu-bold my-2 text-white">{{ __tr('QR Code Generation') }}</h2>
                        <h6 class="ubuntu-bold my-2 text-warning">
                            {{ __tr('Scan QR Code to Start Chat') }}
                        </h6>
                        <p class="text-white">
                            {{ __tr(' Quickly generate QR codes for your WhatsApp phone number with ease using this feature. Users can effortlessly connect by scanning the code with their smartphones, instantly initiating communication with your WhatsApp account. This streamlined process ensures smooth interactions and easy access for engaging with your audience.') }}
                        </p>
                    </div>
                </div>
                <!-- /or code-->

                <!-- FlowiseAI   -->
                <div class="row align-items-center p-3 mb-4 lw-mobile-div-reverse ">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <h2 class="ubuntu-bold my-2 text-white">
                            {{ __tr('AI Bots Integration for Vendors using ') }}<span
                                class="text-warning">{{ __tr('FlowiseAI') }}</span></h2>
                        <p class="text-white">
                            {{ __tr(' Flowise AI offers AI-powered chatbots for vendors to automate customer interactions and enhance engagement.') }}
                        </p>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 text-center">
                        <!-- image -->
                        <img class="img-fluid w-75 mb-4" src="{{ asset('imgs/outer-home/bg-11.png') }}" />
                        <!-- image -->
                    </div>
                </div>
                <!-- /FlowiseAI  -->

                <!-- WhatsApp Cloud API -->
                <div class="row align-items-center p-3 mb-4">
                    <div class="col-sm-12 col-md-6 col-lg-6 text-center">
                        <!-- image -->
                        <img class="img-fluid w-75 mb-4" src="{{ asset('imgs/outer-home/bg-4.png') }}" />
                        <!-- image -->
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <h2 class="ubuntu-bold my-2 text-white">
                            {{ __tr('Powered by WhatsApp Cloud API') }}</h2>
                        <p class="text-white">
                            {!! __tr(' __appName__', ['__appName__' => $appName]) !!}
                            {{ __tr(' seamlessly integrates with the WhatsApp Cloud API, ensuring smooth operations without server management or additional expenses, making it a cost-effective solution.') }}
                        </p>
                    </div>
                </div>
                <!-- /WhatsApp Cloud API -->
            </div>
        </section>
        <!-- /Advance-Feature-section -->

        <!-- Basic features section -->
        <section class="lw-section-block-content py-4">
            <div class="container">
                <div class="row align-items-center p-3 mb-4">
                    <div class="col-sm-12 col-md-6 col-lg-6 text-center">
                        <!-- image -->
                        <img class="img-fluid w-75 mb-4" src="{{ asset('imgs/outer-home/bg-7.png') }}" />
                        <!-- image -->
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <h2 class="ubuntu-bold my-2">{{ __tr('Built for Customer Engagements') }}</h2>
                        <p>{!! __tr(' __appName__', ['__appName__' => $appName])
                            !!}{{ __tr(' is a helpful tool for businesses to communicate better with customers. It makes talking to customers easier and simpler, helping businesses grow and build strong relationships.') }}
                        </p>
                        <div class="lw-button">
                            <a href="{{ route('auth.register') }}" class="btn lw-btn text-white">
                                {{ __tr('Signup Now') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Basic features section -->
        <!-- pricing block -->
        <section class="lw-bg-success lw-pricing-block" id="pricing">
            <div class="container">
                <!-- heading -->
                <h2 class="ubuntu-bold mb-5 text-center text-white">{{ __tr('Simple and Clear Pricing') }}</h2>
                <!-- /heading -->
                {{-- free plan --}}
                <div class="row justify-content-center">
                    @php
                    $freePlanDetails = getFreePlan();
                    $freePlanStructure = getConfigFreePlan();
                    $paidPlans = getPaidPlans();
                    $planStructure = getConfigPaidPlans();
                    @endphp

                    @if ($freePlanDetails['enabled'])
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-4">
                        <div class="card border-0 text-center text-dark p-4 h-100">
                            <!-- title -->
                            <h4 class="mb-4 mt-2 ubuntu-bold">{{ $freePlanDetails['title'] }}</h4>
                            <!-- title -->
                            <!--  pricing -->
                           <h2 class="price mb-1 ubuntu-bold text-danger">{{ formatAmount(0, true, true) }}</h2>
                                    <span>{{  __tr('Monthly') }}</span>
                                    <br>
                                    <h2 class="price mb-1 ubuntu-bold text-danger">{{ formatAmount(0, true, true) }}</h2>
                                    <span>{{  __tr('Yearly') }}</span>
                                    <br>
                            <!--  pricing -->
                            <small><a class="text-muted" target="_blank"
                                    href="https://business.whatsapp.com/products/platform-pricing">{{ __tr('+ WhatsApp Cloud Messaging Charges') }}
                                    <i class="fas fa-external-link-alt "></i></a></small>
                            <hr class="mt-4">
                            {{-- features --}}
                            <ul>
                                @foreach ($freePlanStructure['features'] as $featureKey => $featureValue)
                                @php
                                $configFeatureValue = $featureValue;
                                $featureValue = $freePlanDetails['features'][$featureKey];
                                @endphp
                                <li>
                                    @if (isset($featureValue['type']) and $featureValue['type'] == 'switch')
                                    @if (isset($featureValue['limit']) and $featureValue['limit'])
                                    <i class="fa fa-check mr-3 bg-success"></i>
                                    @else
                                    <i class="fa fa-times mr-3 bg-danger"></i>
                                    @endif
                                    {{ $configFeatureValue['description'] }}
                                    @else
                                    <strong>
                                        @if (isset($featureValue['limit']) and $featureValue['limit'] < 0)
                                            {{ __tr('Unlimited') }} @elseif(isset($featureValue['limit']))
                                            {{ __tr($featureValue['limit']) }} @endif </strong>
                                            {{ $configFeatureValue['description'] }}
                                            {{ $configFeatureValue['limit_duration_title'] ?? '' }}
                                            @endif
                                </li>
                                @endforeach
                            </ul>
                            {{-- /features --}}
                            <div class="pricing-price"></div>
                        </div>
                    </div>
                    <!-- /free plan-->
                    @endif
                    {{-- paid plan --}}
                    @foreach ($planStructure as $planKey => $plan)
                    @php
                    $planId = $plan['id'];
                    $features = $plan['features'];
                    $savedPlan = $paidPlans[$planKey];
                    $charges = $savedPlan['charges'];
                    if (!$savedPlan['enabled']) {
                    continue;
                    }
                    @endphp
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-4">
                        <div class="card border-0 text-center text-dark p-4 h-100">
                            <!-- title -->
                            <h4 class="mb-4 ubuntu-bold">{{ $savedPlan['title'] ?? $plan['title'] }}
                            </h4>
                            <!-- /title -->
                            <!--  pricing -->
                             @foreach ($charges as $itemKey => $itemValue)
                                @php
                                    if(!$itemValue['enabled']) {
                                        continue;
                                    }
                                @endphp
                                <h2 class="price ubuntu-bold text-danger mb-1">{{ formatAmount($itemValue['charge'], true, true) }}</h2>
                                <span>{{ Arr::get($plan['charges'][$itemKey], 'title', '') }}</span>
                                <br>
                            @endforeach
                            <!--  /pricing -->
                            <small><a class="text-muted" target="_blank"
                                    href="https://business.whatsapp.com/products/platform-pricing">{{ __tr('+ WhatsApp Cloud Messaging Charges') }}
                                    <i class="fas fa-external-link-alt"></i></a></small>
                            <hr class="mt-4">
                            {{-- features --}}
                            <ul>
                                @foreach ($plan['features'] as $featureKey => $featureValue)
                                @php
                                $configFeatureValue = $featureValue;
                                $featureValue = $savedPlan['features'][$featureKey];
                                @endphp
                                <li>
                                    @if (isset($featureValue['type']) and $featureValue['type'] == 'switch')
                                    @if (isset($featureValue['limit']) and $featureValue['limit'])
                                    <i class="fa fa-check mr-3 bg-success"></i>
                                    @else
                                    <i class="fa fa-times mr-3 bg-danger"></i>
                                    @endif
                                    {{ $configFeatureValue['description'] }}
                                    @else
                                    <strong>
                                        @if (isset($featureValue['limit']) and $featureValue['limit'] < 0)
                                            {{ __tr('Unlimited') }} @elseif(isset($featureValue['limit']))
                                            {{ __tr($featureValue['limit']) }} @endif </strong>
                                            {{ $configFeatureValue['description'] }}
                                            {{ $configFeatureValue['limit_duration_title'] ?? '' }}
                                            @endif
                                </li>
                                @endforeach
                            </ul>
                            {{-- /features --}}
                            <div class="pricing-price"></div>
                        </div>
                    </div>
                    @endforeach
                    {{-- /paid plan --}}
                </div>
        </section>
        <!-- pricing block -->
        <!-- FAQ -->
        <section class="bg-white lw-faq-block">
            <div class="container my-5">
                <div class="row align-items-center">
                    <div class="col-sm-12 col-md-12 col-lg-5">
                        <h2 class="ubuntu-bold">{{ __tr('Frequently Asked Questions') }}</h2>
                        <p>{{ __tr("Have questions? Here you'll find the answers most valued by our partners, along with access to step-by-step instructions and support.") }}
                        </p>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-7">
                        <div class="accordion" id="faqAccordion">
                            <!-- FAQ Item 1 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-bottom collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        {{ __tr(' How do I sign up for __appName__?', [
                                            '__appName__' => $appName,
                                        ]) }}
                                    </button>
                                </h5>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted p-4">
                                        {{ __tr(
                                            'Signing up for __appName__ is easy and straightforward. Just visit our sign-up page, fill in your details, and follow the instructions to get started.',
                                            [
                                                '__appName__' => $appName,
                                            ],
                                        ) }}
                                    </div>
                                </div>
                            </div>
                            <!-- /FAQ Item 1 -->
                            <!-- FAQ Item 2 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed border-bottom" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                        {{ __tr('Can I import contacts from an existing customer database?') }}
                                    </button>
                                </h5>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted p-4">
                                        {{ __tr(
                                            'Yes, __appName__ supports importing contacts through XLSX files. You can easily upload your existing customer database and start sending messages right away.',
                                            [
                                                '__appName__' => $appName,
                                            ],
                                        ) }}
                                    </div>
                                </div>
                            </div>
                            <!-- /FAQ Item 2 -->
                            <!-- FAQ Item 3 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed border-bottom" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        {{ __tr('What kind of support does __appName__ offer?', [
                                            '__appName__' => $appName,
                                        ]) }}
                                    </button>
                                </h5>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted p-4">
                                        {{ __tr(
                                            '__appName__ offers 24/7 customer support through live chat, email, and phone. Our dedicated team is here to help you with any issues or questions you might have.',
                                            [
                                                '__appName__' => $appName,
                                            ],
                                        ) }}
                                    </div>
                                </div>
                            </div>
                            <!-- /FAQ Item 3 -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- FAQ  -->
        <!-- footer -->
        <footer class="footer-section text-center d-none">
            <div class="container">
                <div class="footer-content p-5 pb-0">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
                            <div class="footer-widget">
                                <div class="footer-logo">
                                    <a href="{{ url('/') }}"><img src="{{ getAppSettings('logo_image_url') }}"
                                            alt="{{ getAppSettings('name') }}" class="img-fluid" alt="logo"></a>
                                </div>
                                <div class="footer-social-icon my-4">
                                    <h3 class="ubuntu-bold">{{ __tr('Follow us') }}</h3>
                                    <a href="#"><i class="fab fa-facebook"></i></a>
                                    <a href="#"><i class="fab fa-twitter"></i></a>
                                    <a href="#"><i class="fab fa-google-plus-g"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                            <div class="footer-widget">
                                <div class="footer-widget-heading">
                                    <h3 class="ubuntu-bold"> {{ __tr('Useful Links') }}</h3>
                                    <div class="border-bottom w-25 m-auto"></div>
                                </div>
                                <ul>
                                    <li><a href="#"> {{ __tr('Home') }}</a></li>
                                    <li><a href="#"> {{ __tr('About') }}</a></li>
                                    <li><a href="#"> {{ __tr('Careers') }}</a></li>
                                    <li><a href="#"> {{ __tr('Our Services') }}</a></li>
                                    <li><a href="#"> {{ __tr('Privacy policy') }}</a></li>
                                    <li><a href="#"> {{ __tr('terms and conditions') }}</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                            <div class="footer-widget">
                                <div class="footer-widget-heading">
                                    <h3 class="ubuntu-bold"> {{ __tr('Contact') }}</h3>
                                    <div class="border-bottom w-25 m-auto"></div>
                                </div>
                                <ul>
                                    @if (getAppSettings('contact_details'))
                                    <div class="lw-ws-pre-line">
                                        <li> {!! getAppSettings('contact_details') !!}</li>
                                    </div>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="copyright-text text-center pb-3">
                    <p> &copy;{{ getAppSettings('name') }} {{ __tr(date('Y')) }}.
                        {{ __tr('All Rights Reserved.') }}</p>
                </div>
            </div>
        </footer>
        <footer class="text-center py-3 lw-bg-success">
            <div class="container px-5">
                <div class="text-white mt-3 small">
                    <div class="mb-2">&copy; {{ getAppSettings('name') }} {{ __tr(date('Y')) }}.
                        {{ __tr('All Rights Reserved.') }}</div>
                </div>
            </div>
        </footer>
        <!-- footer -->
        <script>
        (function() {
            'use strict';
            window.appConfig = {
                debug: "{{ config('app.debug') }}",
                csrf_token: "{{ csrf_token() }}",
                locale: '{{ app()->getLocale() }}',
            }
        })();
        </script>
        {!! __yesset([
        'dist/js/common-vendorlibs.js',
        'dist/js/vendorlibs.js',
        'dist/packages/bootstrap/js/bootstrap.bundle.min.js',
        'dist/js/jsware.js',
        ]) !!}
        {!! getAppSettings('page_footer_code_all') !!}
        @if (isLoggedIn())
        {!! getAppSettings('page_footer_code_logged_user_only') !!}
        @endif
    </body>

</html>