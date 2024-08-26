@php
/**
* Component : User
* Controller : UserController
* File : User.list.blade.php
* ----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => __tr('Team Members')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Team Members'),
'description' => '',
'class' => 'col-lg-7'
])
<div class="container-fluid mt-lg--6">
    <div class="row">
        <!-- button -->
        <div class="col-xl-12 mb-3">
            <button type="button" class="lw-btn btn btn-primary float-right" data-toggle="modal"
                data-target="#lwAddNewUser"> {{ __tr('Add New User') }}</button>
        </div>
        <!--/ button -->
        <!-- Add New User Modal -->
        <x-lw.modal id="lwAddNewUser" :header="__tr('Add New User')" :hasForm="true">
            <!--  Add New User Form -->
            <x-lw.form id="lwAddNewUserForm" :action="route('vendor.user.write.create')"
                :data-callback-params="['modalId' => '#lwAddNewUser', 'datatableId' => '#lwUserList']"
                data-callback="appFuncs.modelSuccessCallback">
                <!-- form body -->
                <div class="lw-form-modal-body">
                    <!-- form fields form fields -->
                    <!-- First_Name -->
                    <x-lw.input-field type="text" id="lwFirstNameField" data-form-group-class=""
                        :label="__tr('First Name')" name="first_name" required="true" />
                    <!-- /First_Name -->
                    <!-- Last_Name -->
                    <x-lw.input-field type="text" id="lwLastNameField" data-form-group-class=""
                        :label="__tr('Last Name')" name="last_name" required="true" />
                    <!-- /Last_Name -->
                    <x-lw.input-field type="number" id="lwMobileNumberField" data-form-group-class=""
                        :label="__tr('Mobile Number')" name="mobile_number" required="true" minlength="9" />
                        <h5><span class="text-muted">{{__tr("Mobile number should be with country code without 0 or +")}}</span></h5>

                    <!-- Username -->
                    <x-lw.input-field type="text" id="lwUsernameField" data-form-group-class=""
                        :label="__tr('Username')" name="username" required="true" minlength="3" />
                    <!-- /Username -->
                    <x-lw.input-field type="text" id="lwEmailField" data-form-group-class="" :label="__tr('Email')"
                        name="email" required="true" minlength="3" />
                    <!-- Password -->
                    <x-lw.input-field type="password" id="lwPasswordField" data-form-group-class=""
                        :label="__tr('Password')" name="password" required="true" minlength="6" />
                    <!-- /Password -->
                    <fieldset>
                        <legend>{{ __tr('Permissions') }}</legend>
                        @foreach (getListOfPermissions() as $permissionKey => $permission)
                        <div class="d-block my-3">
                            <x-lw.checkbox id="lw{{ $permissionKey }}Item" name="permissions[{{ $permissionKey }}]"
                                data-lw-plugin="lwSwitchery" :label="$permission['title']" />
                            @if (isset($permission['description']) and $permission['description'])
                            <p class="text-muted mt-1 fs-1">{{ $permission['description'] }}</p>
                            <hr class="my-1">
                            @endif
                        </div>
                        @endforeach
                    </fieldset>
                 
                </div>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
            <!--/  Add New User Form -->
        </x-lw.modal>
        <!--/ Add New User Modal -->
        <!--/ Edit User Modal -->

        <!-- Edit User Modal -->
        <x-lw.modal id="lwEditUser" :header="__tr('Edit User & Permissions')" :hasForm="true">
            <!--  Edit User Form -->
            <x-lw.form id="lwEditUserForm" :action="route('vendor.user.write.update')"
                :data-callback-params="['modalId' => '#lwEditUser', 'datatableId' => '#lwUserList']"
                data-callback="appFuncs.modelSuccessCallback">
                <!-- form body -->
                <div id="lwEditUserBody" class="lw-form-modal-body"></div>
                <script type="text/template" id="lwEditUserBody-template">

                    <input type="hidden" name="userIdOrUid" value="<%- __tData._uid %>" />
                        <!-- form fields -->
                        <!-- First_Name -->
           <x-lw.input-field type="text" id="lwFirstNameEditField" data-form-group-class="" :label="__tr('First Name')" value="<%- __tData.first_name %>" name="first_name"  required="true"                 />
                <!-- /First_Name -->
                <!-- Last_Name -->
           <x-lw.input-field type="text" id="lwLastNameEditField" data-form-group-class="" :label="__tr('Last Name')" value="<%- __tData.last_name %>" name="last_name"  required="true"                 />
           <x-lw.input-field type="text" id="lwMobileNumberEditField" data-form-group-class="" :label="__tr('Mobile Number')" value="<%- __tData.mobile_number %>" name="mobile_number"  />
            <h5><span class="text-muted">{{__tr("Mobile number should be with country code without 0 or +")}}</span></h5>

           <x-lw.input-field type="text" id="lwEmailEditField" data-form-group-class="" :label="__tr('Email')" value="<%- __tData.email %>" name="email"  />
           <x-lw.input-field type="password" id="lwPasswordEditField" data-form-group-class="" :label="__tr('Password')"  name="password"  />
                <!-- /Last_Name -->
                <!-- STATUS -->
                <div class="form-group pt-3">
                    <label for="lwIsMemberActiveEditField">{{  __tr('Status') }}</label>
                    <input type="checkbox" id="lwIsMemberActiveEditField" <%- __tData.status == 1 ? 'checked' : '' %> data-lw-plugin="lwSwitchery" name="status">
                </div>
                <!-- /STATUS -->
                <fieldset>
                    <legend>{{  __tr('Permissions') }}</legend>
                    @foreach(getListOfPermissions() as $permissionKey => $permission)
                            <span class="d-block my-3">
                                <label for="lwEdit{{ $permissionKey }}Permission" class="flex items-center">
                                    <input id="lwEdit{{ $permissionKey }}Permission" type="checkbox" <%- (__tData.vendor_user_details?.__data?.permissions?.{{ $permissionKey }} == 'allow') ? 'checked' : '' %> name="permissions[{{ $permissionKey }}]" class="form-checkbox" data-lw-plugin="lwSwitchery">
                                    <span class="ml-2 text-gray-600">{{ $permission['title'] }}</span>
                                </label>
                                @if (isset($permission['description']) and $permission['description'])
                                <p class="text-muted mt-1 fs-1">{{ $permission['description'] }}</p>
                                <hr class="my-1">
                                @endif
                            </span>
                    @endforeach
                </fieldset>
                  
                     </script>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
            <!--/  Edit User Form -->
        </x-lw.modal>
        <!--/ Edit User Modal -->
        <div class="col-xl-12">
            <x-lw.datatable id="lwUserList" :url="route('vendor.user.read.list')">
                <th data-orderable="true" data-name="first_name">{{ __tr('First Name') }}</th>
                <th data-orderable="true" data-name="last_name">{{ __tr('Last Name') }}</th>
                <th data-orderable="true" data-name="username">{{ __tr('Username') }}</th>
                <th data-orderable="true" data-name="email">{{ __tr('Email') }}</th>
                <th data-orderable="true" data-name="mobile_number">{{ __tr('Mobile Number') }}</th>
                <th data-orderable="true" data-name="created_at">{{ __tr('Created At') }}</th>
                <th data-orderable="true" data-name="status">{{ __tr('Status') }}</th>
                <th data-template="#userActionColumnTemplate" name="null">{{ __tr('Action') }}</th>
            </x-lw.datatable>
        </div>
        <!-- action template -->
        <script type="text/template" id="userActionColumnTemplate">
            <a data-pre-callback="appFuncs.clearContainer" title="{!! __tr('Edit User & Permissions') !!}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditUserBody" href="<%= __Utils.apiURL("{{ route('vendor.user.read.update.data', [ 'userIdOrUid']) }}", {'userIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditUser"><i class="fa fa-edit"></i> {!! __tr('Edit User & Permissions') !!}</a>
<!--  Delete Action -->
<a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.user.write.delete', [ 'userIdOrUid']) }}", {'userIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action" data-confirm="#lwDeleteUser-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwUserList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a>
{{-- login as  --}}
<!-- login as button -->

<% if(__tData.status=='Active') { %>
<% if(__tData._uid != '{{ getUserUid() }}') { %>
<a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.user.write.login_as', [ 'userIdOrUid']) }}", {'userIdOrUid': __tData._uid}) %>" class="btn btn-success btn-sm lw-ajax-link-action" data-confirm="#lwLoginAs-template" title="{{ __tr('Login as') }}"><i class="fa fa-sign-in-alt"></i> {{  __tr('Login as') }}</a>
<% } %>
<% } %>
<!-- /login as button -->

    </script>
        <!-- /action template -->

        <!-- User delete template -->
        <script type="text/template" id="lwDeleteUser-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this User?') }}</p>
    </script>
        <!-- /User delete template -->
        <script type="text/template" id="lwLoginAs-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
        <p>{{ __tr('You want login to this user account?') }}</p>
</script>
    </div>
</div>
@endsection()