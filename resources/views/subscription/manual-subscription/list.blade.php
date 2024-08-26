@php
/**
* Component : Subscription
* Controller : ManualSubscriptionController
* File : manual_subscription.list.blade.php
----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => __tr('Manual/Prepaid Subscriptions')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Manual/Prepaid Subscriptions'),
'description' => '',
'class' => 'col-lg-7'
])
<?php $isExtendedLicence = (getAppSettings('product_registration', 'licence') === 'dee257a8c3a2656b7d7fbe9a91dd8c7c41d90dc9'); ?>
<div class="container-fluid">
    <div class="row">
                <!-- Edit Manual Subscription Modal -->
                <x-lw.modal id="lwEditManualSubscription" :header="__tr('Update Subscription')" :hasForm="true">
                    <!--  Edit Manual Subscription Form -->
                    <x-lw.form id="lwEditManualSubscriptionForm"
                        :action="route('central.subscription.manual_subscription.write.update')"
                        :data-callback-params="['modalId' => '#lwEditManualSubscription', 'datatableId' => '#lwManualSubscriptionList']"
                        data-callback="appFuncs.modelSuccessCallback">
                        <!-- form body -->
                        <div id="lwEditManualSubscriptionBody" class="lw-form-modal-body"></div>
                        <script type="text/template" id="lwEditManualSubscriptionBody-template">
                            @if ($isExtendedLicence)
                            <fieldset>
                                <legend>{{  __tr('Provided Payment Details') }}</legend>
                                <dl>
                                    <dt>{{  __tr('Payment Method') }}</dt>
                                    <dd><%- __tData.__data?.manual_txn_details?.selected_payment_method %></dd>
                                    <dt>{{  __tr('Transaction Reference') }}</dt>
                                    <dd><%- __tData.__data?.manual_txn_details?.txn_reference %></dd>
                                    <dt>{{  __tr('Transaction Date') }}</dt>
                                    <dd><%- __tData.transactionDate %> </dd>
                                </dl>
                            </fieldset>
                            <input type="hidden" name="manualSubscriptionIdOrUid" value="<%- __tData._uid %>" />
                                <!-- form fields -->
                        <!-- Ends_At -->
                   <x-lw.input-field type="number" min="0" id="lwChargesField" data-form-group-class="" :label="__tr('Charges')" value="<%- __tData.charges %>" name="charges"  required="true" />
                   <x-lw.input-field type="date" id="lwEndsAtEditField" data-form-group-class="" :label="__tr('Expiry At')" value="<%- __tData.ends_at %>" name="ends_at"  required="true" />
                    <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwSubscriptionStatus"
                data-form-group-class="" data-selected="<%- __tData.status %>" :label="__tr('Status')" name="status"
                required="true">
                <x-slot name="selectOptions">
                    <option value="">{{  __tr('Select Status') }}</option>
                    @foreach (configItem('subscription_status') as $subscriptionStatusKey => $subscriptionStatus)
                    <option value="{{ $subscriptionStatusKey }}">{{ $subscriptionStatus }}</option>
                    @endforeach
                </x-slot>
            </x-lw.input-field>
                    <div class="form-group">
                        <label for="lwEditRemarks">{{  __tr('Remarks if any') }}</label>
                        <textarea class="form-control" name="remarks" id="lwEditRemarks" rows="2"><%- __tData.remarks %></textarea>
                    </div>
                        <!-- /Ends_At -->
                        @else
                        <div class="alert alert-danger">
                            {{  __tr('Extended licence required to enable manage subscription') }}
                        </div>
                        @endif
                             </script>
                        <!-- form footer -->
                        <div class="modal-footer">
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                        </div>
                    </x-lw.form>
                    <!--/  Edit Manual Subscription Form -->
                </x-lw.modal>
                <!--/ Edit Manual Subscription Modal -->
        <div class="col-xl-12">
            <x-lw.datatable data-page-length="100" id="lwManualSubscriptionList"
                :url="route('central.subscription.manual_subscription.read.list')">
                <th data-template="#manualSubscriptionVendorColumnTemplate" data-name="null">{{ __tr('Vendor') }}</th>
                <th data-orderable="true" data-name="plan_id">{{ __tr('Plan') }}</th>
                <th data-order-by="true" data-order-type="desc" data-orderable="true" data-name="created_at">{{ __tr('Created At') }}</th>
                <th data-orderable="true" data-name="ends_at">{{ __tr('Expiry At') }}</th>
                <th data-orderable="true" data-name="charges">{{ __tr('Plan Charges') }}</th>
                <th data-orderable="true" data-name="charges_frequency">{{ __tr('Frequency') }}</th>
                <th data-template="#manualSubscriptionStatusColumnTemplate" data-name="null">{{ __tr('Status') }}</th>
                <th data-template="#manualSubscriptionActionColumnTemplate" name="null">{{ __tr('Action') }}</th>
            </x-lw.datatable>
            <script type="text/template" id="manualSubscriptionStatusColumnTemplate">
                <% if(__tData.status == 'Pending') { %>
                    <span class="badge badge-warning">{{  __tr('Pending') }}</span>
                <% } else if(__tData.status == 'Active') { %>
                    <span class="badge badge-success">{{  __tr('Active') }}</span>
                <% }  else { %>
                    <%- __tData.status %>
                <% } %>
                <% if(__tData.options.is_expired) { %>
                    <span class="badge badge-danger">{{  __tr('Expired') }}</span>
                <% } %>
            </script>
                    <!-- action template -->
        <script type="text/template" id="manualSubscriptionActionColumnTemplate">
            <a class="btn btn-primary btn-sm" href ="<%= __Utils.apiURL("{{ route('central.vendor.details',['vendorIdOrUid'=>'vendorIdOrUid'])}}", {'vendorIdOrUid':__tData.vendor_uid}) %>"> {{  __tr('Subscription') }} </a>
            {{-- update expiry --}}
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Update') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditManualSubscriptionBody" href="<%= __Utils.apiURL("{{ route('central.subscription.manual_subscription.read.update.data', [ 'manualSubscriptionIdOrUid']) }}", {'manualSubscriptionIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditManualSubscription"><i class="fa fa-edit"></i> {{  __tr('Update') }}</a>
<!--  Delete Action -->
<a data-method="post" href="<%= __Utils.apiURL("{{ route('central.subscription.manual_subscription.write.delete', [ 'manualSubscriptionIdOrUid']) }}", {'manualSubscriptionIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteManualSubscription-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwManualSubscriptionList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a>
    </script>
        <!-- /action template -->
        <!-- Manual Subscription delete template -->
        <script type="text/template" id="lwDeleteManualSubscription-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this Subscription?') }}</p>
    </script>
        <!-- /Manual Subscription delete template -->
            <script type="text/template" id="manualSubscriptionVendorColumnTemplate">
                <a  href ="<%= __Utils.apiURL("{{ route('vendor.dashboard',['vendorIdOrUid'=>'vendorIdOrUid'])}}", {'vendorIdOrUid':__tData.vendor_uid}) %>"> <%-__tData.vendor_title %> </a>
                <% if(__tData.status == 'pending') { %>
                    <span class="badge badge-danger">{{  __tr('Action Required') }}</span>
                <% } %>
            </script>
        </div>
    </div>
</div>
@endsection()