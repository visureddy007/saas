@php
/**
* Component     : SubscriptionPlan
* Controller    : SubscriptionPlanController
* File          : plan.list.blade.php
----------------------------------------------------------------------------- */
@endphp

@extends('layouts.app', ['title' => __tr('Plan List')])

@section('content')
    @include('users.partials.header', [
    'title' => __tr('Plan List') . ' '. auth()->user()->name,
    'description' => '',
    'class' => 'col-lg-7'
    ])

    <div class="container-fluid mt-lg--6">
        <div class="row">

            <!-- button -->
            <div class="col-xl-12 mb-3">
                <button type="button" class="lw-btn btn btn-primary float-right" data-toggle="modal"
                    data-target="#lwAddNewPlan"> <?= __tr('Add New Plan') ?></button>
                        </div>
                        <!--/ button -->
                        <!-- Add New Plan Modal -->
                            <x-lw.modal id="lwAddNewPlan" :header="__tr('Add New Subscription Plan')" :hasForm="true">
                                <!--  Add New Plan Form -->
                                <x-lw.form id="lwAddNewPlanForm" :action="route('subscription_plan.plan.write.create')"  :data-callback-params="['modalId' => '#lwAddNewPlan', 'datatableId' => '#lwPlanList']" data-callback="appFuncs.modelSuccessCallback">
                                    <!-- form body -->
                                    <div class="lw-form-modal-body">
                                        <!-- form fields form fields -->
                                        <!-- Title -->
                                    <div class="form-group">
                                <label for="lwTitleField"><?= __tr('Title') ?></label>
                                <input type="text" class="lw-form-field form-control" placeholder="<?= __tr('Title') ?>" id="lwTitleField"  name="title"  ng-required="true"      ng-minlength="3"      ng-maxlength="100"           />
                            </div>
                        <!-- /Title -->
                                <!-- Description -->
                                        <div class="form-group">
                                    <label for="lwDescriptionField"><?= __tr('Description') ?></label>
                                    <textarea cols="10" rows="3" id="lwDescriptionField"  class="lw-form-field form-control" placeholder="<?= __tr('Description') ?>" name="description"     ng-minlength="6"             ></textarea>
                                </div>
                        <!-- /Description -->
                                <!-- Price -->
                                        <div class="form-group">
                                    <label for="lwPriceField"><?= __tr('Price') ?></label>
                                    <input type="number" class="lw-form-field form-control" placeholder="<?= __tr('Price') ?>"  name="price"      min="0"               />
                                </div>
                        <!-- /Price -->
                                <!-- Duration -->
                                        <div class="form-group">
                                    <label for="lwDurationField"><?= __tr('Duration') ?></label>
                                    <input type="number" class="lw-form-field form-control" placeholder="<?= __tr('Duration') ?>"  name="duration"      min="1"               />
                                </div>
                        <!-- /Duration -->
                                <!-- Status -->
                                        <div class="form-group">
                                    <label for="lwStatusField"><?= __tr('Status') ?></label>
                                    <input type="number" class="lw-form-field form-control" placeholder="<?= __tr('Status') ?>"  name="status"                  />
                                </div>
                        <!-- /Status -->
                                     </div>
                                    <!-- form footer -->
                                    <div class="modal-footer">
                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-primary">{{ __tr('Submit') }}</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                                    </div>
                                </x-lw.form>
                                <!--/  Add New Plan Form -->
                            </x-lw.modal>
                            <!--/ Add New Plan Modal -->
                           
                                        <!-- Details Plan Modal -->
                        <x-lw.modal id="lwDetailsPlan" :header="__tr('Subscription Plan Details')">
                            <!--  Details Plan Form -->
                            <!-- Details body --> 
                            <div data-default-text="{{ __tr('Please wait while we fetch data') }}" id="lwDetailsPlanBody" class="lw-form-modal-body"></div>
                            <script type="text/template" id="lwDetailsPlanBody-template">
                                <!-- form fields -->
                                <div>
                        <label class="small"><?= __tr('Title') ?>:</label>
                        <div class="lw-details-item"><%- __tData.title %></div>
                    </div>
                             
                    <div>
                        <label class="small"><?= __tr('Description') ?>:</label>
                        <div class="lw-details-item"><%- __tData.description %></div>
                    </div>
                             
                    <div>
                        <label class="small"><?= __tr('Price') ?>:</label>
                        <div class="lw-details-item"><%- __tData.price %></div>
                    </div>
                             
                    <div>
                        <label class="small"><?= __tr('Duration') ?>:</label>
                        <div class="lw-details-item"><%- __tData.duration %></div>
                    </div>
                             
                    <div>
                        <label class="small"><?= __tr('Status') ?>:</label>
                        <div class="lw-details-item"><%- __tData.status %></div>
                    </div>
                                 </script>
                            <!--/  Details Plan Form -->
                        </x-lw.modal>
                        <!--/ Edit Plan Modal -->

                                <!-- Edit Plan Modal -->
                                <x-lw.modal id="lwEditPlan" :header="__tr('Edit Subscription Plan')" :hasForm="true">
                                <!--  Edit Plan Form -->
                                <x-lw.form id="lwEditPlanForm" :action="route('subscription_plan.plan.write.update')"  :data-callback-params="['modalId' => '#lwEditPlan', 'datatableId' => '#lwPlanList']" data-callback="appFuncs.modelSuccessCallback">
                                    <!-- form body --> 
                                    <div data-default-text="{{ __tr('Please wait while we fetch data') }}" id="lwEditPlanBody" class="lw-form-modal-body"></div>
                                    <script type="text/template" id="lwEditPlanBody-template">
                                        
                                        <input type="hidden" name="subscriptionPlanIdOrUid" value="<%- __tData._uid %>" />
                                        <!-- form fields -->
                                        <!-- Title -->
                                    <div class="form-group">
                                <label for="lwTitleField"><?= __tr('Title') ?></label>
                                <input type="text" class="lw-form-field form-control" placeholder="<?= __tr('Title') ?>" id="lwTitleField" value="<%- __tData.title %>" name="title"  ng-required="true"      ng-minlength="3"      ng-maxlength="100"           />
                            </div>
                        <!-- /Title -->
                                <!-- Description -->
                                        <div class="form-group">
                                    <label for="lwDescriptionField"><?= __tr('Description') ?></label>
                                    <textarea cols="10" rows="3" id="lwDescriptionField" value="<%- __tData.description %>" class="lw-form-field form-control" placeholder="<?= __tr('Description') ?>" name="description"     ng-minlength="6"             ><%- __tData.description %></textarea>
                                </div>
                        <!-- /Description -->
                                <!-- Price -->
                                        <div class="form-group">
                                    <label for="lwPriceField"><?= __tr('Price') ?></label>
                                    <input type="number" class="lw-form-field form-control" placeholder="<?= __tr('Price') ?>" value="<%- __tData.price %>" name="price"      min="0"               />
                                </div>
                        <!-- /Price -->
                                <!-- Duration -->
                                        <div class="form-group">
                                    <label for="lwDurationField"><?= __tr('Duration') ?></label>
                                    <input type="number" class="lw-form-field form-control" placeholder="<?= __tr('Duration') ?>" value="<%- __tData.duration %>" name="duration"      min="1"               />
                                </div>
                        <!-- /Duration -->
                                <!-- Status -->
                                        <div class="form-group">
                                    <label for="lwStatusField"><?= __tr('Status') ?></label>
                                    <input type="number" class="lw-form-field form-control" placeholder="<?= __tr('Status') ?>" value="<%- __tData.status %>" name="status"                  />
                                </div>
                        <!-- /Status -->
                                     </script>
                                    <!-- form footer -->
                                    <div class="modal-footer">
                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-primary">{{ __tr('Submit') }}</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                                    </div>
                                </x-lw.form>
                                <!--/  Edit Plan Form -->
                            </x-lw.modal>
                            <!--/ Edit Plan Modal -->
                                        <div class="col-xl-12">
                                        <x-lw.datatable id="lwPlanList" :url="route('subscription_plan.plan.read.list')">
                                                <th  data-orderable="true"  data-name="title"><?= __tr('Title') ?></th>
                                                 <th  data-name="description"><?= __tr('Description') ?></th>
                                                 <th  data-orderable="true"  data-name="price"><?= __tr('Price') ?></th>
                                                 <th  data-orderable="true"  data-name="duration"><?= __tr('Duration') ?></th>
                                                 <th  data-orderable="true"  data-name="status"><?= __tr('Status') ?></th>
                                                 <th data-template="#planActionColumnTemplate" name="null"><?= __tr('Action') ?></th>
                                            </x-lw.datatable>
                            
                            
                        </div>
                                
                        <!-- action template -->
                        <script type="text/template" id="planActionColumnTemplate">
                                        <a data-pre-callback="appFuncs.clearContainer" title="{{ __tr('Details') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwDetailsPlanBody" href="<%= __Utils.apiURL("{{ route('subscription_plan.plan.read.update.data', ['subscriptionPlanIdOrUid']) }}", {'subscriptionPlanIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwDetailsPlan"><i class="fa fa-edit"></i> {{ __tr('Details') }}</a>
                            <a data-pre-callback="appFuncs.clearContainer" title="{{ __tr('Edit') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditPlanBody" href="<%= __Utils.apiURL("{{ route('subscription_plan.plan.read.update.data', ['subscriptionPlanIdOrUid']) }}", {'subscriptionPlanIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditPlan"><i class="fa fa-edit"></i> {{ __tr('Edit') }}</a>
                 
                <!--  Delete Action -->
                <a data-method="post" href="<%= __Utils.apiURL("{{ route('subscription_plan.plan.write.delete', ['subscriptionPlanIdOrUid']) }}", {'subscriptionPlanIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeletePlan-template" title="{{ __tr('Delete') }}" data-toggle="modal" data-target="#deletePlan" data-callback-params="{{ json_encode(['datatableId' => '#lwPlanList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{ __tr('Delete') }}</a>
                    </script>
                <!-- /action template -->
                     
                    <!-- Plan delete template -->
                    <script type="text/template" id="lwDeletePlan-template">
                            <h2><?= __tr('Are You Sure!') ?></h2>
                            <p><?= __tr('You want to delete this Plan?') ?></p>
                    </script>
                    <!-- /Plan delete template -->
                        </div>
                </div>
@endsection()
