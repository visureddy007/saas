@extends('layouts.app', ['title' => __tr('Bot Flow Builder')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Bot Flow Builder'),
'description' => '',
'class' => 'col-lg-7'
])
{!! __yesset([
'static-assets/packages/jquery.flowchart/jquery.flowchart.min.css'
]) !!}
<div class="container-fluid mt-lg--6">
    <div class="row" x-data="{isAdvanceBot:'interactive',botFlowUid:'{{ $botFlowUid }}'}">
        <!-- button -->
        <div class="col-xl-12 mb-3">
            <div class="float-right">
                <a class="lw-btn btn btn-secondary" href="{{ route('vendor.bot_reply.bot_flow.read.list_view') }}">{{
                        __tr('Back to Bot Flows') }}</a>
            </div>
        </div>
        <!--/ button -->
        <div class="col-xl-12" x-data="initialAlpineData">
           <div class="row">
            <div class="card col-12">
                <div class="card-header">
                    <span class="h2">{{ $botFlow->title }}</span>
                    <div class="float-right">
                        <span class="form-group m-0 mr-3">
                            <label for="lwUpdateStatusSwitch">
                                <input data-lw-plugin="lwSwitchery" @click="function() {
                                    __DataRequest.post('{{ route('vendor.bot_reply.bot_flow_data.write.update') }}', {
                                        'botFlowUid' : '{{ $botFlowUid }}',
                                        'bot_flow_status' : (!botFlowStatusValue ? 1 : 0)
                                        }, function() {});
                                }" {{ ($botFlow->status == 1) ? 'checked' : '' }} x-model="botFlowStatusValue" value="1" class="custom-checkbox" id="lwUpdateStatusSwitch" type="checkbox" name="bot_flow_status">
                                {{  __tr('Status') }}
                            </label>
                        </span>
                        <template x-if="isUnsavedContent">
                            <div class="btn-group">
                            <button @click="window.unsavedAlert()" type="button" class="btn btn-primary dropdown-toggle" aria-expanded="false">
                            {{ __tr('Add New Bot Reply') }}
                        </button>
                            </div>
                        </template>
                        <template x-if="!isUnsavedContent">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                    {{ __tr('Add New Bot Reply') }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <button type="button" @click="isAdvanceBot = 'simple'" class="dropdown-item btn"
                                        data-toggle="modal" data-target="#lwAddNewAdvanceBotReply"> {{ __tr('Simple Bot Reply')
                                        }}</button>
                                    <button type="button" @click="isAdvanceBot = 'media'" class="dropdown-item btn"
                                        data-toggle="modal" data-target="#lwAddNewAdvanceBotReply"> {{ __tr('Media Bot Reply')
                                        }}</button>
                                    <button type="button" @click="isAdvanceBot = 'interactive'" class="dropdown-item btn"
                                        data-toggle="modal" data-target="#lwAddNewAdvanceBotReply"> {{ __tr('Advance Interactive Bot Reply') }}</button>
                                </div>
                            </div>
                        </template>
                        <button class="btn btn-warning" @click="saveData"><i class="fa fa-save"></i> {{  __tr('Save') }}</button>
                    </div>
                </div>
                {{-- added just to initialize --}}
                <div class="pl-1 m-1 lw-flow-builder-container-holder" dir="ltr">
                    <template x-text="processedFlowBots"></template>
                    <div class="lw-flow-builder-container p-4 card-body" id="lwBotFlowBuilder"></div>
                </div>
            </div>
           </div>
        </div>
        @include('bot-reply.bot-forms-partial')
    </div>
</div>
  <!-- Bot Reply delete template -->
  <script type="text/template" id="lwDeleteBotReply-template">
    <h2>{{ __tr('Are You Sure!') }}</h2>
    <p>{{ __tr('You want to delete this Bot Reply?') }}</p>
</script>
<!-- /Bot Reply delete template -->
  <!-- Bot Reply duplicate template -->
  <script type="text/template" id="lwDuplicateBotReply-template">
    <h2>{{ __tr('Are You Sure!') }}</h2>
    <p>{{ __tr('Are you sure you want to duplicate this Bot Reply?') }}</p>
</script>
<!-- /Bot Reply duplicate template -->
@push('js')
{!! __yesset([
    'static-assets/packages/jqueryui-1.13.3/jquery-ui.min.js',
    // 'static-assets/packages/others/jquery.mousewheel.min.js',
    'static-assets/packages/others/jquery.panzoom.min.js',
    'static-assets/packages/jquery.flowchart/jquery.flowchart.min.js'
]) !!}
@endpush
<script>
    var data = {
        links : {}
    };
        window.flowchartData = {};
        window.$flowBuilderInstance = null;
        window.__isUnsavedContent = false;
        window.isFlowChatInitialized = false;
</script>
@push('appScripts')
<script>
    $(document).ready(function() {
       'use strict';
        window.$flowBuilderInstance = $('#lwBotFlowBuilder').flowchart({
            data: {},
            defaultSelectedLinkColor: '#000055',
            grid: 10,
            multipleLinksOnInput: true,
            multipleLinksOnOutput: true,
            linkWidth:5,
            defaultLinkColor:'green',
            defaultSelectedLinkColor:'skyblue',
            onOperatorSelect : function(elementUid) {
                return true;
            },
            onLinkCreate : function(linkId, linkData) {
                data.links[linkId] = linkData;
                if(window.isFlowChatInitialized) {
                    window.updateDraft();
                };
                return true;
            },
            onLinkSelect : function(linkId, linkData) {
                $('.lw-operator-link-'+data.links[linkId]['toOperator']).show();
                return true;
            },
            onLinkUnselect : function(linkId) {
                $('.lw-delete-link-btn').hide();
                return true;
            },
            onLinkDelete : function(linkId) {
                delete data.links[linkId];
                window.updateDraft();
                return true;
            },
            onOperatorMoved : function(operatorId, position) {
                window.updateDraft();
            },
        });
         // Panzoom initialization...
        /*
        @link https://github.com/timmywil/panzoom/tree/v3.2.2
        */
        window.$flowBuilderInstance.panzoom({
            contain: 'automatic',
            cursor: "grab"
        });
    // required to trigger default flow
    __DataRequest.updateModels({
        tempClick : '{{ uniqid() }}'
    });
    window.onBotReplyDeleted = function(response) {
        window.$flowBuilderInstance.flowchart('deleteOperator', response.data.botReplyUid);
        _.defer(function() {
            window.saveFlowChartData();
        });
        appFuncs.modelSuccessCallback(response);
    };
    window.unsavedAlert = function() {
        showConfirmation('{{ __tr('You have unsaved changes. You need to save it first, Do you want to save it now?') }}', function() {
            window.saveFlowChartData();
        });
    };
    window.updateDraft = function(response) {
        window.__isUnsavedContent = true;
        __DataRequest.updateModels({
            isUnsavedContent : true
        });
        return true;
    };
    window.saveFlowChartData = function() {
        window.isFlowChatInitialized = false;
        __DataRequest.post("{{ route('vendor.bot_reply.bot_flow_data.write.update') }}", {
            'botFlowUid' : '{{ $botFlowUid }}',
            'flow_chart_data' : window.$flowBuilderInstance.flowchart('getData')
            }, function() {
                window.__isUnsavedContent = false;
                // __Utils.viewReload();
        });
    };
    window.onbeforeunload = function (e) {
        if(window.__isUnsavedContent) {
            var message = "{{ __tr('Changes that you made may not be saved.') }}",
            e = e || window.event;
            // For IE and Firefox
            if (e) {
                e.returnValue = message;
            }
            // For Safari
            return message;
        };
    };
    _.defer(function() {
        window.isFlowChatInitialized = true;
    });
});
</script>
@endpush
<script>
(function() {
    'use strict';
    document.addEventListener('alpine:init', () => {
        Alpine.data('initialAlpineData', () => ({
            tempClick:false,
            isUnsavedContent:false,
            botFlowStatusValue:{{ $botFlow->status == 1 ?: 0 }},
            saveData:function() {
                if(window.$flowBuilderInstance) {
                    window.saveFlowChartData();
                };
                return {};
            },
            flowBots: @json($flowBots),
            botFlowData: @json($botFlow->__data['flow_builder_data'] ?? []),
            processedFlowBots: function () {
                var xyz = this.tempClick;
                _.merge(data, this.botFlowData, {
                    operators : {
                        start : {
                            top: 10,
                            left: 10,
                            properties: {
                                title: "{{ __tr('Start') }} ->",
                                outputs: {
                                    start_output : {
                                        label : '{{ $botFlow->start_trigger }}'
                                    }
                                }
                            }
                        }
                    }
                });
                var index = 1;
                for (const flowBotIndex in this.flowBots) {
                    if (Object.hasOwnProperty.call(this.flowBots, flowBotIndex)) {
                        const element = this.flowBots[flowBotIndex];
                        data.operators[element._uid] = {
                            top: _.get(data.operators[element._uid],'top', _.random(150, 200)),
                            left: _.get(data.operators[element._uid],'left', _.random(20, 100)),
                            properties: {
                                title: element.name,
                                body: `<div class="btn-group btn-group-sm"><a class="btn btn-default" style="padding: 1px;"></a> <a style="display:none;" x-show="isUnsavedContent" @click.prevent="window.unsavedAlert()" title="{{  __tr('Edit') }}" class="btn btn-default btn-sm" href="#"><i class="fa fa-edit"></i> {{  __tr('Edit') }}</a> <a x-show="!isUnsavedContent" data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Edit') }}" class="btn btn-default lw-ajax-link-action" data-response-template="#lwEditBotReplyBody" href="`+__Utils.apiURL("{{ route('vendor.bot_reply.read.update.data', [ 'botReplyIdOrUid']) }}", {'botReplyIdOrUid': element._uid})+`" data-toggle="modal" data-target="#lwEditBotReply"><i class="fa fa-edit"></i> {{  __tr('Edit') }}</a> <a style="display:none;" x-show="isUnsavedContent" @click.prevent="window.unsavedAlert()" title="{{  __tr('Delete') }}" class="btn btn-danger btn-sm" href="#"><i class="fa fa-trash"></i> {{  __tr('Delete') }} </a>
                                    <a x-show="!isUnsavedContent" data-method="post" href="`+ __Utils.apiURL("{{ route('vendor.bot_reply.write.delete', [ 'botReplyIdOrUid']) }}", {'botReplyIdOrUid': element._uid}) +`" class="btn btn-danger lw-ajax-link-action" data-confirm="#lwDeleteBotReply-template" title="{{ __tr('Delete') }}" data-callback="onBotReplyDeleted"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a> <a style="display:none;" x-show="isUnsavedContent" @click.prevent="window.unsavedAlert()" title="{{  __tr('Duplicate') }}" class="btn btn-light btn-sm" href="#"><i class="fa fa-copy"></i></a> <a x-show="!isUnsavedContent" data-method="post" href="`+ __Utils.apiURL("{{ route('vendor.bot_reply.write.duplicate', [ 'botReplyIdOrUid']) }}", {'botReplyIdOrUid': element._uid}) +`" class="btn btn-light lw-ajax-link-action" data-confirm="#lwDuplicateBotReply-template" title="{{ __tr('Duplicate') }}"><i class="fa fa-copy"></i></a>
                                    <a class="btn btn-light" style="padding: 1px;"></a></div><button style="display:none;" class="lw-delete-link-btn lw-operator-link-`+element._uid+` btn btn-warning btn-block btn-sm" @click="window.$flowBuilderInstance.flowchart('deleteSelected');"><i class="fas fa-unlink"></i> {{  __tr('Delete Link') }}</button>`,
                                inputs: {
                                    input: {
                                        label: "-->"
                                    },
                                },
                                outputs: {}
                            }
                        };
                        if(_.get(element.__data, 'interaction_message')) {
                            if(_.get(element.__data, 'interaction_message.buttons')) {
                                for (const interactiveButtonIndex in element.__data.interaction_message.buttons) {
                                    if (Object.hasOwnProperty.call(element.__data.interaction_message.buttons, interactiveButtonIndex)) {
                                        const buttonElement = element.__data.interaction_message.buttons[interactiveButtonIndex];
                                        data.operators[element._uid]['properties']['outputs'][interactiveButtonIndex] = {
                                            label: buttonElement
                                        };
                                    };
                                };
                            };
                            if(_.get(element.__data, 'interaction_message.list_data.sections')) {
                                for (const interactiveListSectionIndex in element.__data.interaction_message.list_data.sections) {
                                    if (Object.hasOwnProperty.call(element.__data.interaction_message.list_data.sections, interactiveListSectionIndex)) {
                                        const sectionElement = element.__data.interaction_message.list_data.sections[interactiveListSectionIndex];
                                        if(_.get(sectionElement, 'rows')) {
                                            for (const rowIndex in sectionElement.rows) {
                                                if (Object.hasOwnProperty.call(sectionElement.rows, rowIndex)) {
                                                    const rowElement = sectionElement.rows[rowIndex];
                                                        data.operators[element._uid]['properties']['outputs']['sections___' + interactiveListSectionIndex + '___rows___' + rowIndex + '___title'] = {
                                                        label: rowElement['title']
                                                    };
                                                };
                                            };
                                        };
                                    };
                                };
                            };
                        };
                        index++;
                    };
                };
                if(window.$flowBuilderInstance) {
                    window.$flowBuilderInstance.flowchart('setData', data);
                };
            }
        }));
    });
})();
</script>
@endsection()