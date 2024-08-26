@php
/**
* Component : BotReply
* Controller : BotReplyController
* File : BotReply.list.blade.php
* ----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => __tr('Bot Replies')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Bot Replies'),
'description' => '',
'class' => 'col-lg-7'
])
<div class="container-fluid mt-lg--6">
    <div class="row" x-data="{isAdvanceBot:'interactive',botFlowUid:null}">
        <!-- button -->
        <div class="col-xl-12 mb-3">
            <div class="float-right">
                <!-- Example single danger button -->
                <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                {{  __tr('Create New Bot') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                <button type="button" @click="isAdvanceBot = 'simple'" class="dropdown-item btn" data-toggle="modal"
                data-target="#lwAddNewAdvanceBotReply"> {{ __tr('Simple Bot Reply') }}</button>
                <button type="button" @click="isAdvanceBot = 'media'"  class="dropdown-item btn" data-toggle="modal"
                data-target="#lwAddNewAdvanceBotReply"> {{ __tr('Media Bot Reply') }}</button>
                <button type="button" @click="isAdvanceBot = 'interactive'"  class="dropdown-item btn" data-toggle="modal"
                data-target="#lwAddNewAdvanceBotReply"> {{ __tr('Advance Interactive Bot Reply') }}</button>
                </div>
            </div>
                <x-lw.help-modal :subject="__tr('What are the Bots Replies and How to use it?')">
                    <h3>{{  __tr('Whats are Bots') }}</h3>
                    <p>{{  __tr('Bots are instructions given to the system so when you get message you can set reply message so it will get triggered automatically.') }}</p>
                    </x-lw.help-modal>
            </div>
        </div>
        <!--/ button -->
       
        <div class="col-xl-12">
            <x-lw.datatable data-page-length="100" id="lwBotReplyList" :url="route('vendor.bot_reply.read.list')">
                <th data-orderable="true" data-name="name">{{ __tr('Name') }}</th>
                <th data-name="bot_type">{{ __tr('Bot Type') }}</th>
                <th data-orderable="true" data-name="trigger_type">{{ __tr('Trigger Type') }}</th>
                <th data-orderable="true" data-name="reply_trigger">{{ __tr('Trigger Subject') }}</th>
                <th data-orderable="true" data-name="status">{{ __tr('Status') }}</th>
                <th data-orderable="true" data-name="created_at">{{ __tr('Created At') }}</th>
                <th data-template="#botReplyActionColumnTemplate" name="null">{{ __tr('Action') }}</th>
            </x-lw.datatable>
            @include('bot-reply.bot-forms-partial')
        </div>
        <!-- action template -->
        <script type="text/template" id="botReplyActionColumnTemplate">
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Edit') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditBotReplyBody" href="<%= __Utils.apiURL("{{ route('vendor.bot_reply.read.update.data', [ 'botReplyIdOrUid']) }}", {'botReplyIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditBotReply"><i class="fa fa-edit"></i> {{  __tr('Edit') }}</a>
<!--  Delete Action -->
<a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.bot_reply.write.delete', [ 'botReplyIdOrUid']) }}", {'botReplyIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteBotReply-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwBotReplyList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a>
<!--  Duplicate Action -->
<a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.bot_reply.write.duplicate', [ 'botReplyIdOrUid']) }}", {'botReplyIdOrUid': __tData._uid}) %>" class="btn btn-light btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDuplicateBotReply-template" title="{{ __tr('Duplicate') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwBotReplyList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-copy"></i> {{  __tr('Duplicate') }}</a>
    </script>
        <!-- /action template -->

        <!-- Bot Reply delete template -->
        <script type="text/template" id="lwDeleteBotReply-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this Bot Reply?') }}</p>
    </script>
        <!-- /Bot Reply delete template -->
        <!-- Bot Reply duplicate template -->
        <script type="text/template" id="lwDuplicateBotReply-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to duplicate this Bot Reply?') }}</p>
    </script>
        <!-- /Bot Reply duplicate template -->
    </div>
</div>
@endsection()