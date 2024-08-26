@extends('layouts.app', ['title' => __tr('Dashboard')])
@php
$vendorIdOrUid = $vendorIdOrUid ?? getVendorUid();
if(!isset($vendorViewBySuperAdmin)) {
$vendorViewBySuperAdmin = null;
}
@endphp
@section('content')
@if(hasCentralAccess())
@include('users.partials.header', [
'title' => __tr('__vendorTitle__ Dashboard', [
'__vendorTitle__' => $vendorInfo['title'] ?? getVendorSettings('title')
]),
'description' => '',
// 'class' => 'col-lg-7'
])
@else
@include('users.partials.header', [
'title' => __tr('Hi __userFullName__,', [
'__userFullName__' => getUserAuthInfo('profile.first_name')
]),

'description' => '',
// 'class' => 'col-lg-7'
])
@endif
<div class="container-fluid">
    @if(hasCentralAccess())
    @php
    $currentActivePlanDetails = getVendorCurrentActiveSubscription($vendorInfo['id']);
    $planDetails = vendorPlanDetails(null, null, $vendorInfo['id']);
    @endphp
    <div class="col-xl-12 p-0">
        <!-- breadcrumbs -->
        <nav aria-label="breadcrumb" class="lw-breadcrumb-container">
            <ol class="breadcrumb bg-transparent text-light p-0 m-0">
                <li class=" breadcrumb-item mb-3">
                    <a class="text-decoration-none" href="{{ route('central.vendors') }}">{{ __tr('Manage Vendors')
                        }}</a>

                </li>
                <li class="text-light breadcrumb-item" aria-current="page">{{ __tr('Dashboard') }}</li>
            </ol>
        </nav>
        <!-- /breadcrumbs -->
    </div>
    <br>
    @if(hasVendorAccess() or $vendorViewBySuperAdmin )
<div class="container-fluid">
    @if (getVendorSettings('whatsapp_access_token_expired', null, null, $vendorIdOrUid))
    <div class="alert alert-danger">
        {{ __tr('Your WhatsApp token seems to be expired, Generate new token, prefer creating permanent token and
        save.') }}
        <br>
        <a class="btn btn-sm btn-white my-2"
            href="{{ route('vendor.settings.read', ['pageType' => 'whatsapp-cloud-api-setup']) }}">{{ __tr('Cloud API
            setup') }}</a>
    </div>
    @elseif (!isWhatsAppBusinessAccountReady($vendorIdOrUid))
    <div class="alert alert-danger">
        {{ __tr('You are not ready to send messages, WhatsApp Setup is Incomplete') }}
        <br>
        <a class="btn btn-sm btn-white my-2"
            href="{{ route('vendor.settings.read', ['pageType' => 'whatsapp-cloud-api-setup']) }}">{{ __tr('Complete
            your WhatsApp Cloud API setup') }}</a>
    </div>
    @endif
    @if (getAppSettings('pusher_by_vendor') and !getVendorSettings('pusher_app_id', null, null, $vendorIdOrUid))
    <div class="alert alert-warning">
        {{ __tr('Pusher keys needs to setup for realtime communication like Chat etc., You can get it from
        __pusherLink__, choose channel and create the app to get the required keys.', [
        '__pusherLink__' => '<a target="blank" href="https://pusher.com">pusher.com</a>'
        ]) }}
        <br>
        <a class="btn btn-sm btn-white my-2"
            href="{{ route('vendor.settings.read', ['pageType' => 'general']) }}#pusherKeysConfiguration">{{
            __tr('Pusher Configuration') }}</a>
    </div>
    @endif
    @if(!$vendorViewBySuperAdmin)
    <div class="row">
        <div class="col-12 mb-5">
            <fieldset>
                <legend>{{ __tr('Quick Start') }}</legend>
                <h3>
                    <ol>
                        <li>{{ __tr('Login to your Facebook Account') }}</li>
                        <li>{!! __tr('Complete Setup as Shown in __cloudApiSetupLink__', [
                            '__cloudApiSetupLink__' => '<a
                                href="'. route('vendor.settings.read', ['pageType' => 'whatsapp-cloud-api-setup']) .'">'.
                                __tr('WhatsApp Cloud API Setup').'</a>'
                            ]) !!}</li>
                        <li>{!! __tr('Manage and Sync WhatsApp templates at __manageContactsLink__',[
                            '__manageContactsLink__' => '<a
                                href="'. route('vendor.whatsapp_service.templates.read.list_view') .'">'. __tr('Manage
                                WhatsApp Templates').'</a>'
                            ]) !!}</li>
                        <li>{!! __tr('Create your contact groups using __manageGroupsLink__', [
                            '__manageGroupsLink__' => '<a href="'. route('vendor.contact.group.read.list_view') .'">'.
                                __tr('Manage Groups').'</a>'
                            ]) !!}</li>
                        <li>{!! __tr('Create your Contacts or Upload excel file with predefined exportable template at
                            __manageContactsLink__',[
                            '__manageContactsLink__' => '<a href="'. route('vendor.contact.read.list_view') .'">'.
                                __tr('Manage Contacts').'</a>'
                            ]) !!}</li>
                        <li>{!! __tr('Create & Schedule your Campaigns at __manageCampaignsLink__',[
                            '__manageCampaignsLink__' => '<a href="'. route('vendor.campaign.read.list_view') .'">'.
                                __tr('Manage Campaigns').'</a>'
                            ]) !!}</li>
                    </ol>
                </h3>
            </fieldset>
        </div>
    </div>
    @endif
</div>
@endif


    <div class="col-xl-12 pl-1">
        <div class="">
            <div class="card-body">
                <fieldset class="mb-5">
                    <legend>{{ __tr('Vendor Details') }}</legend>
                     <div class="col-xl-12 ">
                        <a data-method="post" class="btn btn-light btn-sm lw-ajax-link-action float-right" href="{{ route('central.vendors.user.write.login_as',['vendorUid'=>$vendorIdOrUid])}}"   data-confirm="#lwLoginAs-template" title="{{ __tr('Login as Vendor Admin') }}"><i class="fa fa-sign-in-alt"></i> {{  __tr('Login') }}</a>
                    </div>
                    <div class="my-2 ">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Vendor Title:') }}</h4>
                        <p class="card-text">{{$vendorInfo['title']}} </p>
                    </div>
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Account Status:') }}</h4>
                        @if($vendorInfo['status']==0)
                        <p class="card-text">{{__tr('Inactive') }}</p>
                        @else
                        <p class="card-text">{{configItem('status_codes',$vendorInfo['status'])}}</p>
                        @endif

                    </div>
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Created On:') }}</h4>
                        <p class="card-text">{{formatDate($vendorUserData['created_at'])}}</p>
                    </div>
                    <hr>
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Admin User Name:') }}</h4>
                        <p class="card-text">{{$vendorUserData['first_name'] . ' ' . $vendorUserData['last_name']}}</p>
                    </div>
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Username:') }}</h4>
                        <p class="card-text">{{$vendorUserData['username']}}</p>
                    </div>
                    @if($vendorUserData['mobile_number'])
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Phone Number:') }}</h4>
                        <p class="card-text">{{$vendorUserData['mobile_number']}}</p>
                    </div>
                    @endif
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Email:') }}</h4>
                        <p class="card-text">{{$vendorUserData['email']}}</p>
                    </div>
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Admin User Status:') }}</h4>
                        @if($vendorUserData['status']==0)
                        <p class="card-text">{{__tr('Inactive') }}</p>
                        @else
                        <p class="card-text">{{configItem('status_codes', $vendorUserData['status'])}}</p>
                        @endif
                    </div>
                </fieldset>

                <fieldset class="mb-4">
                    @php
                    $planStructure = $planDetails->plan_id ? getPaidPlans($planDetails->plan_id) : getFreePlan();
                    $planCharges = $planStructure['charges'][$planDetails->frequency] ?? null;
                    @endphp
                    <legend>{{ __tr('Current Subscribed Plan') }}</legend>
                    <div class="col-xl-12  ">
                        <a class="btn btn-primary btn-sm  float-right" href="{{ route('central.vendor.details',['vendorIdOrUid'=>$vendorIdOrUid])}}" title="{{ __tr('Subscription') }}"> {{  __tr('Subscription') }}</a>
                    </div>
                    @if ($planDetails->hasActivePlan())
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Plan Title:') }}</h4>
                        <p class="card-text">{{$planDetails->planTitle()}} </p>
                    </div>
                    @if($planCharges)
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Current Plan Charges:') }}</h4>
                        <p class="card-text"> {{ $planCharges['title'] ?? '' }} {{ formatAmount($planCharges['charge'],
                            true) }}</p>
                    </div>
                    @endif
                    @if($currentActivePlanDetails)
                    @if($planDetails['subscription_type']=='manual')
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Status:') }}</h4>
                        <p class="card-text">{{configItem('subscription_status',$currentActivePlanDetails['status'])}}</p>
                    </div>
                    @elseif($planDetails['subscription_type']=='auto')
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Status:') }}</h4>
                        <p class="card-text">{{configItem('subscription_status',$currentActivePlanDetails['stripe_status'])}}</p>
                    </div>
                    @else
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Status:') }}</h4>
                        <p class="card-text">{{__tr('Active') }}</p>
                    </div>
                    @endif
                    @endif
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Subscription Type:') }}</h4>
                        <p class="card-text">{{configItem('subscription_methods',$planDetails['subscription_type'])}}</p>
                    </div>
                    @if($currentActivePlanDetails)
                     {{--  check payment mathod is manual for payment method --}}
                    @if($planDetails['subscription_type']=='manual')
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Payment Method:') }}</h4>
                        <p class="card-text">{{ $currentActivePlanDetails['__data']['manual_txn_details']['selected_payment_method'] ?? 'NA' }}</p>
                    </div>
                    @endif
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Created On:') }}</h4>
                        <p class="card-text">{{formatDate($currentActivePlanDetails['created_at'])}}</p>
                    </div>
                    @endif
                    <div class="my-2">
                        <h4 class="text-dark font-weight-bold">{{ __tr('Expire On:') }}</h4>
                        <p class="card-text">{{ $planDetails['ends_at'] ? formatDate($planDetails['ends_at']):  'NA'}}</p>
                    </div>
                    @else
                    <div class="alert alert-warning">{{ __tr('Vendor does not have any active plan.') }}</div>
                    @endif

                </fieldset>

            </div>
        </div>
    </div>

    @endif
</div>
<script type="text/template" id="lwLoginAs-template">
    <h2>{{ __tr('Are You Sure!') }}</h2>
    <p>{{ __tr('You want login to this vendor admin account?') }}</p>
</script>
@if(isDemo() and isDemoVendorAccount() and hasVendorAccess())
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="alert alert-dark">
                <h2 class="text-white">{{ __tr('Demo Account') }}</h2>
                <p>{{ __tr('Contacts created here with your numbers will be deleted frequently.') }}</p>
                <p>{{ __tr('If you want to test system with your own account. Facebook also provides Test Number which
                    is very easy to setup and test. You can follow the steps given in Quick Start on dashboard to get
                    started.') }}</p>
            </div>
        </div>
    </div>
</div>
@endif
@include('layouts.headers.cards')

@push('head')
<?= __yesset(['dist/css/dashboard.css'],true) ?>
@endpush
@push('js')
<?= __yesset(['dist/js/dashboard.js'],true)?>
@endpush
@endsection()