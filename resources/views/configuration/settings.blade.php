@php
/**
* Component     : Configuration
* Controller    : ConfigurationController
* File          : configurations.settings.blade.php
----------------------------------------------------------------------------- */
@endphp

@extends('layouts.app', ['title' => __tr('Settings')])

@section('content')
    @include('users.partials.header', [
    'title' => __tr('Settings') . ' '. auth()->user()->name,
    'description' => '',
    'class' => 'col-lg-7'
    ])
    <?php $pageType = request()->pageType; ?>
    <div class="container-fluid">
       <div class="row">
        <div class="col-lg-12">
            <!-- card start -->
            <div class="card">
                <!-- card body -->
                <div class="card-body">
                    <!-- include related view -->
                    @include('configuration.'. $pageType)
                    <!-- /include related view -->
                </div>
                <!-- /card body -->
            </div>
            <!-- card start -->
        </div>
       </div>
    </div>
    </div>
@endsection()
