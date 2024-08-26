@extends('layouts.app', ['title' => __tr('Subscriptions')])

@section('content')
@include('users.partials.header', [
'title' => __tr('Subscriptions'),
'description' => '',
'class' => 'col-lg-7'
])

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            {{-- DATATABLE --}}
            <x-lw.datatable id="lwManageVendorsTable" :url="route('central.subscription.read.list')" data-page-length="100">
                <th data-template="#titleExtendedButtons" data-orderable="true" data-name="title">
                    <?= __tr('Vendor') ?>
                </th>
                <th data-orderable="true" data-name="plan_type">
                    <?= __tr('Plan') ?>
                </th>
                <th data-orderable="true" data-name="stripe_id">
                    <?= __tr('Stripe ID') ?>
                </th>
                <th data-orderable="true" data-name="stripe_status">
                    <?= __tr('Stripe Status') ?>
                </th>
                <th data-orderable="true" data-name="stripe_price">
                    <?= __tr('Stripe Price Plan') ?>
                </th>
                <th data-orderable="true" data-name="created_at">
                    <?= __tr('Created At') ?>
                </th>
                <th data-orderable="true" data-name="ends_at">
                    <?= __tr('Ends At') ?>
                </th>

            </x-lw.datatable>
            {{-- DATATABLE --}}
        </div>
    </div>
</div>
<script type="text/template" id="titleExtendedButtons">
    <% if(_.isEmpty(__tData.title)){ %>
        NA
    <% } else { %>
        <a  href ="<%= __Utils.apiURL("{{ route('vendor.dashboard',['vendorIdOrUid'=>'vendorIdOrUid'])}}", {'vendorIdOrUid':__tData._uid}) %>"> <%-__tData.title %> </a>
    <% }  %>
</script>
@endsection
