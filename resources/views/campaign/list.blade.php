@php
/**
* Component : Campaign
* Controller : CampaignController
* File : campaign.list.blade.php
* -----------------------------------------------------------------------------
*/
@endphp
@extends('layouts.app', ['title' => __tr('Campaigns')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Campaigns'),
'description' => '',
'class' => 'col-lg-7'
])

<?php $status = request()->status ?? 'active'; ?>
<div class="container-fluid mt-lg--6">
    <div class="row">
        <!-- button -->
        <div class="col-xl-12 mb-3">
                <a class="lw-btn btn btn-primary float-right" href="{{ route('vendor.campaign.new.view') }}">{{ __tr('Create New Campaign') }}</a>
        </div>
        <!--/ button -->
        <ul class="nav nav-tabs">
        <!-- Active tab -->
					<li class="nav-item">
						<a class="nav-link <?= $status == 'active' ? 'active' : '' ?>" data-title="{{ __tr('Active ') }}" href="<?= route('vendor.campaign.read.list_view', ['status' => 'active']) ?>">
							<?= __tr('Active') ?>
						</a>
					</li>
					<!-- /Active tab -->

					<!-- Archive tab -->
					<li class="nav-item">
						<a class="nav-link <?= $status == 'archived' ? 'active' : '' ?>  " data-title="{{ __tr('Archive') }}" href="<?= route('vendor.campaign.read.list_view', ['status' => 'archived']) ?>">
							<?= __tr('Archive') ?>
						</a>
					</li>
					<!-- /Archive tab -->
				</ul>

        <div class="col-xl-12">
            <x-lw.datatable data-page-length="100" id="lwCampaignList" :url="route('vendor.campaign.read.list', ['status' => $status])">
                <th data-orderable="true" data-name="title">{{ __tr('Title') }}</th>
                <th data-orderable="true" data-name="template_name">{{ __tr('Template') }}</th>
                <th data-orderable="true" data-name="template_language">{{ __tr('Template Language') }}</th>
                <th data-orderable="true" data-name="created_at">{{ __tr('Created At') }}</th>
                <th data-orderable="true" data-order-type="desc" data-order-by="true" data-name="scheduled_at">{{ __tr('Schedule At') }}</th>
                <th data-template="#campaignStatusColumnTemplate" name="null">{!! __tr('Status') !!}</th>
                <th data-template="#campaignActionColumnTemplate" name="null">{!! __tr('Action') !!}</th>
            </x-lw.datatable>
        </div>
        <!-- action template -->
        <script type="text/template" id="campaignActionColumnTemplate">
        <a href="<%= __Utils.apiURL("{{ route('vendor.campaign.status.view', ['campaignUid' => 'campaignUid',]) }}", {'campaignUid': __tData._uid}) %>" class="btn btn-dark btn-sm" title="{{ __tr('Campaign Details') }}"><i class="fa fa-tachometer"></i> {{  __tr('Campaign Dashboard') }}</a>
<!--  Delete Action -->
<% if(__tData.delete_allowed) { %>
<a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.campaign.write.delete', [ 'campaignIdOrUid']) }}", {'campaignIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteCampaign-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwCampaignList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a>
<% } else { %>
    <!--  Archived button -->
    <% if(__tData.status != 5) { %>
        <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.campaign.write.archive', [ 'campaignIdOrUid']) }}", {'campaignIdOrUid': __tData._uid}) %>" class="btn btn-warning btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwArchiveCampaign-template" title="{{ __tr('Archive') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwCampaignList']) }}" data-callback="appFuncs.modelSuccessCallback">{{  __tr('Archive') }}</a>
        <% } else { %>
             <!--  /Archived button -->
            <!--  UnArchived button -->
            <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.campaign.write.unarchive', [ 'campaignIdOrUid']) }}", {'campaignIdOrUid': __tData._uid}) %>" class="btn btn-warning btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwUnArchiveCampaign-template" title="{{ __tr('Unarchive') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwCampaignList']) }}" data-callback="appFuncs.modelSuccessCallback">{{  __tr('Unarchive') }}</a>

            <% } %>
               <!--  /UnArchived button -->

    <% } %>
    </script>
        <!-- /action template -->
        <!-- action template -->
        <script type="text/template" id="campaignStatusColumnTemplate">
<!--  status -->
<% if(__tData.delete_allowed) { %>
    <span class="badge badge-success"><%- __tData.scheduled_status %></span>
<% } else { %>
    <span class="badge badge-warning p-2"><%- __tData.scheduled_status %></span>
<% } %>
    </script>
        <!-- /status template -->

        <!-- Campaign delete template -->
        <script type="text/template" id="lwDeleteCampaign-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this Campaign?') }}</p>
    </script>
        <!-- /Campaign delete template -->
         <!-- Campaign archive template -->
         <script type="text/template" id="lwArchiveCampaign-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to archive this Campaign?') }}</p>
    </script>
        <!-- /Campaign archive template -->
           <!-- Campaign archive template -->
           <script type="text/template" id="lwUnArchiveCampaign-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to unarchive this Campaign?') }}</p>
    </script>
        <!-- /Campaign archive template -->
    </div>
</div>
@endsection()