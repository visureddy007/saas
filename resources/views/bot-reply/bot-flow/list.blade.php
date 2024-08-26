@extends('layouts.app', ['title' => __tr('Bot Flows')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Bot Flows'),
'description' => '',
'class' => 'col-lg-7'
])
<div class="container-fluid mt-lg--6">
    <div class="row">
        <!-- button -->
        <div class="col-xl-12 mb-3">
            <button type="button" class="lw-btn btn btn-primary float-right" data-toggle="modal"
                data-target="#lwAddNewBotFlow"> {{ __tr('Add New Bot Flow') }}</button>
        </div>
        <!--/ button -->
        <!-- Add New Bot Flow Modal -->
        <x-lw.modal id="lwAddNewBotFlow" :header="__tr('Add New Bot Flow')" :hasForm="true">
            <!--  Add New Bot Flow Form -->
            <x-lw.form id="lwAddNewBotFlowForm" :action="route('vendor.bot_reply.bot_flow.write.create')"
                :data-callback-params="['modalId' => '#lwAddNewBotFlow', 'datatableId' => '#lwBotFlowList']"
                data-callback="appFuncs.modelSuccessCallback">
                <!-- form body -->
                <div class="lw-form-modal-body">
                    <!-- form fields form fields -->
                    <!-- Title -->
                    <x-lw.input-field type="text" id="lwTitleField" data-form-group-class="" :label="__tr('Title')"
                        name="title" required="true" minlength="1" maxlength="150" />
                    <!-- /Title -->
                    <!-- Start Trigger -->
                    <x-lw.input-field type="text" id="lwStartTriggerField" data-form-group-class="" :label="__tr('Start Trigger Subject')" name="start_trigger" required="true" minlength="1" maxlength="255" />
                    <!-- /Start Trigger -->
                </div>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
            <!--/  Add New Bot Flow Form -->
        </x-lw.modal>
        <!--/ Add New Bot Flow Modal -->
        <!-- Edit Bot Flow Modal -->
        <x-lw.modal id="lwEditBotFlow" :header="__tr('Edit Bot Flow')" :hasForm="true">
            <!--  Edit Bot Flow Form -->
            <x-lw.form id="lwEditBotFlowForm" :action="route('vendor.bot_reply.bot_flow.write.update')"
                :data-callback-params="['modalId' => '#lwEditBotFlow', 'datatableId' => '#lwBotFlowList']"
                data-callback="appFuncs.modelSuccessCallback">
                <!-- form body -->
                <div id="lwEditBotFlowBody" class="lw-form-modal-body"></div>
                <script type="text/template" id="lwEditBotFlowBody-template">

                    <input type="hidden" name="botFlowIdOrUid" value="<%- __tData._uid %>" />
                        <!-- form fields -->
                        <!-- Title -->
           <x-lw.input-field type="text" id="lwTitleEditField" data-form-group-class="" :label="__tr('Title')" value="<%- __tData.title %>" name="title"  required="true"      minlength="1"      maxlength="150"           />
                <!-- /Title -->
                        <!-- Start Trigger -->
           <x-lw.input-field type="text" id="lwStartTriggerEditField" data-form-group-class="" :label="__tr('Start Trigger Subject')" value="<%- __tData.start_trigger %>" name="start_trigger"  required="true"    minlength="1"      maxlength="255"           />
                <!-- /Start Trigger -->
                <div class="form-group pt-3">
                    <input type="checkbox" id="lwEditBotFlowStatus" <%- __tData.status == 1 ? 'checked' : '' %> data-lw-plugin="lwSwitchery" value="1" name="status">
                    <label for="lwEditBotFlowStatus">{{  __tr('Status') }}</label>
                </div>
                     </script>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
            <!--/  Edit Bot Flow Form -->
        </x-lw.modal>
        <!--/ Edit Bot Flow Modal -->
        <div class="col-xl-12">
            <x-lw.datatable id="lwBotFlowList" :url="route('vendor.bot_reply.bot_flow.read.list')">
                <th data-orderable="true" data-name="title">{{ __tr('Title') }}</th>
                <th data-orderable="true" data-name="start_trigger">{{ __tr('Start Trigger Subject') }}</th>
                <th data-orderable="true" data-name="status">{{ __tr('Status') }}</th>
                <th data-template="#botFlowActionColumnTemplate" name="null">{{ __tr('Action') }}</th>
            </x-lw.datatable>
        </div>
        <!-- action template -->
        <script type="text/template" id="botFlowActionColumnTemplate">
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Edit') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditBotFlowBody" href="<%= __Utils.apiURL("{{ route('vendor.bot_reply.bot_flow.read.update.data', [ 'botFlowIdOrUid']) }}", {'botFlowIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditBotFlow"><i class="fa fa-edit"></i> {{  __tr('Edit') }}</a>
<!--  Delete Action -->
<a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.bot_reply.bot_flow.write.delete', [ 'botFlowIdOrUid']) }}", {'botFlowIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteBotFlow-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwBotFlowList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a> 
<a title="{{  __tr('Flow Builder') }}" class="lw-btn btn btn-sm btn-primary" href="<%= __Utils.apiURL("{{ route('vendor.bot_reply.bot_flow.builder.read.view', [ 'botFlowIdOrUid']) }}", {'botFlowIdOrUid': __tData._uid}) %>"><i class="fas fa-project-diagram"></i> {{  __tr('Flow Builder') }}</a>
    </script>
        <!-- /action template -->

        <!-- Bot Flow delete template -->
        <script type="text/template" id="lwDeleteBotFlow-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this Bot Flow?') }}</p>
    </script>
        <!-- /Bot Flow delete template -->
    </div>
</div>
@endsection()