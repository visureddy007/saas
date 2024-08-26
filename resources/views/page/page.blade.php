@php
/**
* Component     : Page
* Controller    : PageController
* File          : page.list.blade.php
----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => __tr('Page List')])
@section('content')
    @include('users.partials.header', [
    'title' => __tr('Page - __pageTitle__', [
        '__pageTitle__' => $pageData['title']
    ]),
    'description' => '',
    'class' => 'col-lg-7'
    ])
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{ $pageData['content'] }}
            </div>
        </div>
    </div>
@endsection()
