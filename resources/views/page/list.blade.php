@php
/**
* Component     : Page
* Controller    : PageController
* File          : page.list.blade.php
----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => __tr('Pages')])
@section('content')
    @include('users.partials.header', [
    'title' => __tr('Pages'),
    'description' => '',
    'class' => 'col-lg-7'
    ])
   
    <div class="container-fluid">
        <div class="row">
            <!-- button -->
            <div class="col-xl-12 mb-3 mt-md--5">
                <button type="button" class="lw-btn btn btn-primary float-right" data-toggle="modal"
                    data-target="#lwAddNewPage"> <?= __tr('Add New Page') ?></button>
                    </div>
                    <!--/ button -->
                    <!-- Add New Page Modal -->
                <x-lw.modal modal-dialog-class="modal-lg" id="lwAddNewPage" :header="__tr('Add New Page')" :hasForm="true">
                    <!--  Add New Page Form -->
                    <x-lw.form id="lwAddNewPageForm" :action="route('page.write.create')"  :data-callback-params="['modalId' => '#lwAddNewPage', 'datatableId' => '#lwPageList']" data-callback="afterSuccessfullyCreated">
                        <!-- form body -->
                        <div class="lw-form-modal-body">
                            <!-- form fields form fields -->
                        <!-- Title -->
                        <div class="form-group">
                            <label for="lwTitleField"><?= __tr('Title') ?></label>
                            <input type="text" class="lw-form-field form-control" placeholder="<?= __tr('Title') ?>" id="lwTitleField"  name="title"  required="true" />
                        </div>
                            <!-- /Title -->
                            <!-- Slug -->
                        <div class="form-group">
                            <label for="lwSlugField"><?= __tr('Slug') ?></label>
                            <input type="text" class="lw-form-field form-control" placeholder="<?= __tr('Slug') ?>" id="lwSlugField"  name="slug"  required="true"                 />
                            <small class="form-text">
                                {{  __tr('It will be used in url') }}
                            </small>
                        </div>
                            <!-- /Slug -->
                            <!-- Description -->
                            <div class="form-group">
                                <label for="lwDescriptionField"><?= __tr('Description') ?></label>
                                <textarea cols="10" rows="3" id="lwDescriptionField"  class="lw-form-field form-control" placeholder="<?= __tr('Description') ?>" name="description"  required="true" ></textarea>
                            </div>
                            <!-- /Description -->
                             <!-- Show in menu -->
                             <div class="form-group">
                                <input type="checkbox" name="show_in_menu" id="lwshowinMenuField" data-lw-plugin="lwSwitchery" class="lw-form-field js-switch" ui-switch="" />
                                <label for="lwshowinMenuField"><?= __tr('Show in menu') ?></label>
                            </div>
                            <!-- /Show in menu -->
                            <!-- Status -->
                            <div class="form-group">
                                <input type="checkbox" name="status" id="lwStatusField" data-lw-plugin="lwSwitchery" class="lw-form-field js-switch" ui-switch="" />
                                <label for="lwStatusField"><?= __tr('Status') ?></label>
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
                    <!--/  Add New Page Form -->
                </x-lw.modal>
                <!--/ Add New Page Modal -->
                <!-- Details Page Modal -->
                    <x-lw.modal id="lwDetailsPage" :header="__tr('Page Details')">
                        <!--  Details Page Form -->
                        <!-- Details body --> 
                        <div id="lwDetailsPageBody" class="lw-form-modal-body"></div>
                        <script type="text/template" id="lwDetailsPageBody-template">
                            <!-- form fields -->
                            <div>
                                    <label class="small"><?= __tr('Title') ?>:</label>
                                    <div class="lw-details-item"><%- __tData.title %></div>
                                </div>
                                <div>
                                    <label class="small"><?= __tr('Slug') ?>:</label>
                                    <div class="lw-details-item"><%- __tData.slug %></div>
                                </div>
                                <div>
                                
                                    <label class="small"><?= __tr('Description') ?>:</label>
                                    <div class="lw-details-item"><%- __tData.content %></div>
                                </div>
                                <div>
                                    <label class="small"><?= __tr('Status') ?>:</label>
                                    <% if(__tData.status == 1) { %>
                                        <div class="lw-details-item">Active</div>
                                        <% } else { %>
                                            <div class="lw-details-item">Inactive</div>
                                            <% } %>
                                </div>
                             </script>
                        <!--/  Details Page Form -->
                    </x-lw.modal>
                    <!--/ Edit Page Modal -->
                            <!-- Edit Page Modal -->
                            <x-lw.modal id="lwEditPage" :header="__tr('Edit Page')" :hasForm="true">
                            <!--  Edit Page Form -->
                            <x-lw.form id="lwEditPageForm" :action="route('page.write.update')"  :data-callback-params="['datatableId' => '#lwPageList']" data-callback="afterSuccessfullyCreated">
                                <!-- form body -->
                                <div id="lwEditPageBody" class="lw-form-modal-body"></div>
                                <script type="text/template" id="lwEditPageBody-template">
                                    <input type="hidden" name="pageIdOrUid" value="<%- __tData._uid %>" />
                                    <!-- form fields -->
                                    <!-- Title -->
                        <div class="form-group">
                            <label for="lwTitleEditField"><?= __tr('Title') ?></label>
                            <input type="text" class="lw-form-field form-control" placeholder="<?= __tr('Title') ?>" id="lwTitleEditField" value="<%- __tData.title %>" name="title"  required="true" />
                        </div>
                            <!-- /Title -->
                            <!-- Slug -->
                        <div class="form-group">
                            <label for="lwSlugEditField"><?= __tr('Slug') ?></label>
                            <input type="text" class="lw-form-field form-control" placeholder="<?= __tr('Slug') ?>" id="lwSlugEditField" value="<%- __tData.slug %>" name="slug"  required="true"/>
                            <small class="form-text">
                                {{  __tr('It will be used in url') }}
                            </small>
                        </div>
                            <!-- /Slug -->
                            <!-- Description -->
                            <div class="form-group">
                                <label for="lwDescriptionEditField"><?= __tr('Description') ?></label>
                                <textarea cols="10" rows="3" id="lwDescriptionEditField" value="<%- __tData.description %>" class="lw-form-field form-control" placeholder="<?= __tr('Description') ?>" name="description"  required="true"><%- __tData.content %></textarea>
                            </div>
                            <!-- /Description -->
                             <!-- Show in menu -->
                             <div class="form-group">
                                <input type="checkbox" name="show_in_menu" class="lw-form-field js-switch" ui-switch="" id="lwshowinMenuField" data-lw-plugin="lwSwitchery" <%- __tData.show_in_menu == 1 ? 'checked' : '' %>>
                                <label for="lwshowinMenuField"><?= __tr('Show in menu') ?></label>
                            </div>
                            <!-- /Show in menu -->
                            <!-- Status -->
                            <div class="form-group">
                                <input type="checkbox" id="lwStatusEditField"  class="lw-form-field js-switch" name="status" data-lw-plugin="lwSwitchery" <%- __tData.status == 1 ? 'checked' : '' %>  >
                                <label for="lwStatusEditField"><?= __tr('Status') ?></label>
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
                            <!--/  Edit Page Form -->
                        </x-lw.modal>
                        <!--/ Edit Page Modal -->
                        <div class="col-xl-12">
                            <x-lw.datatable id="lwPageList" :url="route('page.read.list')">
                                <th  data-orderable="true" data-template="#pageTemplate" data-name="title"><?= __tr('Title') ?></th>
                                    <th  data-orderable="true"  data-name="slug"><?= __tr('Slug') ?></th>
                                    <th  data-name="formattedContent"><?= __tr('Description') ?></th>
                                    <th  data-orderable="true" data-name="status"><?= __tr('Status') ?></th>
                                    <th data-template="#pageActionColumnTemplate" name="null"><?= __tr('Action') ?></th>
                            </x-lw.datatable>
                    </div>
                    <!-- action template -->
                      <!--title link action template  -->
                    <script type="text/_template" id="pageTemplate">
                        <!-- Preview URL -->

                                <a  target="_blank" href="<%= __tData.preview_url %>"></i><%= __tData.title %> <small><i
                                    class="fa fa-external-link-alt"></i></small></a>
                                <!-- /Preview URL -->
                    </script>
                      <!--title link action template -->
                    <script type="text/template" id="pageActionColumnTemplate">
                                    <a data-pre-callback="appFuncs.clearContainer" title="{{ __tr('Details') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwDetailsPageBody" href="<%= __Utils.apiURL("{{ route('page.read.update.data', ['pageIdOrUid']) }}", {'pageIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwDetailsPage"><i class="fa fa-info-circle"></i> {{ __tr('Details') }}</a>
                        <a data-pre-callback="appFuncs.clearContainer" title="{{ __tr('Edit') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditPageBody" href="<%= __Utils.apiURL("{{ route('page.read.update.data', ['pageIdOrUid']) }}", {'pageIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditPage"><i class="fa fa-edit"></i> {{ __tr('Edit') }}</a>
            <!--  Delete Action -->
            <a data-method="post" href="<%= __Utils.apiURL("{{ route('page.write.delete', ['pageIdOrUid']) }}", {'pageIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeletePage-template" title="{{ __tr('Delete') }}" data-toggle="modal" data-target="#deletePage" data-callback-params="{{ json_encode(['datatableId' => '#lwPageList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{ __tr('Delete') }}</a>
                </script>
            <!-- /action template -->
                <!-- Page delete template -->
                <script type="text/template" id="lwDeletePage-template">
                        <h2><?= __tr('Are You Sure!') ?></h2>
                        <p><?= __tr('You want to delete this Page?') ?></p>
                </script>
                <!-- /Page delete template -->
                    </div>
                    @push('appScripts')
                <script>
                    (function($) {
                        'use strict';
                        window.afterSuccessfullyCreated = function (responseData) {
                        if (responseData.reaction == 1) {
                            __Utils.viewReload();
                        }
                    }
                    })(jQuery);
                </script>
                @endpush
            </div>
@endsection()
