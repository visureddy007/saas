@extends('layouts.app', ['class' => 'main-content-has-bg'])

@section('content')
@include('layouts.headers.guest')

<div class="container lw-guest-page-container-block pb-2" id="pageTop">
    <!-- Table -->
    <div class="row justify-content-center mt-6">
        <div class="card o-hidden border-0 shadow-lg col-xl-8 col-lg-8 col-md-12 mr-md-2 mb-sm-2 p-0">
            {{-- <img class="card-img-top" src="{{ asset('imgs/email-us.jpeg') }}" alt=""> --}}
            <div class="card-header text-center mt-4">
                <div class="col-md-8 offset-md-2 col-sm-12">
                    <img class="card-img-top px-6" src="{{ getAppSettings('logo_image_url') }}" alt="">
                </div>
                <p>{{  __tr('We\'re here to help and answer any question you might have. We look forward to hearing from you!') }}</p>
                <hr>
                <h1 class="mt-4">{{ __tr('Contact us') }}</h1>
                @if (getAppSettings('contact_details'))
                <div class="lw-ws-pre-line">
                    {!! getAppSettings('contact_details') !!}
                </div>
                    <hr>
                @endif
                <i class="fa fa-at fa-3x text-primary"></i>
            </div>
          <div class="card-body">
            <form class="mx-md-3 user lw-ajax-form lw-form " id="lwContactMailForm" method="post" action="<?= route('user.contact.process') ?>" data-show-processing="true">
                <!-- First Name -->
                <div class="form-group">
                 <div class="input-group input-group-alternative mb-1">
                     <div class="input-group-prepend">
                         <span class="input-group-text"><i class="fa fa-user"></i></span>
                     </div>
                     <input class="form-control" id="floatingFullName" placeholder="{{ __tr('Full Name') }}" type="text" name="full_name" value="{{ old('full_name') }}" required>
                 </div>
             </div>

              <!-- Email address -->
              <div class="form-group">
                 <div class="input-group input-group-alternative mb-1">
                     <div class="input-group-prepend">
                         <span class="input-group-text"><i class="fa fa-at"></i></span>
                     </div>
                     <input class="form-control" id="floatingInput" placeholder="{{ __tr('Email') }}" type="email" name="email" value="{{ old('email') }}" required>
                 </div>
             </div>
                     <!-- Subject -->
                     <div class="form-group">
                         <div class="input-group input-group-alternative mb-1">
                             <div class="input-group-prepend">
                                 <span class="input-group-text"><i class="fa fa-book"></i></span>
                             </div>
                             <input class="form-control" id="floatingSubject" placeholder="{{ __tr('Subject') }}" type="text" name="subject" value="{{ old('full_name') }}" required>
                         </div>
                     </div>
                    <!-- Message -->
                    <div class="form-group">
                     <div class="mb-1">
                        <textarea class="form-control" rows="10" id="floatingTextarea" placeholder="{{ __tr('Message') }}"  name="message"  required></textarea>
                     </div>
                 </div>
                 @if(getAppSettings('enable_recaptcha'))
                 <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                 <div class="g-recaptcha form-group" data-sitekey="{{ getAppSettings('recaptcha_site_key') }}"
                     style="transform:scale(0.77); transform-origin:0 0;"></div>
                 @endif
               <!-- create account action -->
               <div class="text-center">
                 <button type="submit" class="btn btn-primary btn-lg my-3">{{ __tr('Submit') }}</button>
             </div>
            </form>
          </div>
        </div>
    </div>
</div>


@endsection