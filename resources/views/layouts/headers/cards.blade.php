@php
if(!isset($vendorViewBySuperAdmin))
$vendorViewBySuperAdmin = false;
@endphp
@if (hasCentralAccess() and !$vendorViewBySuperAdmin )
<div class="header pb-5 pt-2 pt-md-7">
    <div class="container-fluid">
        <div class="header-body" x-cloak x-data="{totalVendors:{{ $totalVendors }},totalActiveVendors:{{ $totalActiveVendors }},totalCampaigns:{{ $totalCampaigns }},messagesInQueue:{{ $messagesInQueue }},totalContacts:{{ $totalContacts }},totalMessagesProcessed:{{ $totalMessagesProcessed }} }">
            <!-- Card stats -->
            <div class="row">
                <div class="col-md-6 col-lg col-sm-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Vendors') }}
                                    </h5>
                                    <span class="h2 font-weight-bold mb-0"
                                        x-text="__Utils.formatAsLocaleNumber(totalVendors)"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-store text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-muted text-sm">
                                <span>{{ __tr('Total Vendors in the system') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg col-sm-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Active Vendors') }}</h5>
                                    <span class="h2 font-weight-bold mb-0"
                                        x-text="__Utils.formatAsLocaleNumber(totalActiveVendors)"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-store text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Contacts') }}</h5>
                                    <span class="h2 font-weight-bold mb-0"
                                        x-text="__Utils.formatAsLocaleNumber(totalContacts)"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-users text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-md-4">
                <div class="col-md-6 col-lg col-sm-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Campaigns') }}</h5>
                                    <span class="h2 font-weight-bold mb-0"
                                        x-text="__Utils.formatAsLocaleNumber(totalCampaigns)"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                        <i class="fas fa-bullhorn text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg col-sm-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Messages in Queue') }}</h5>
                                    <span class="h2 font-weight-bold mb-0"
                                        x-text="__Utils.formatAsLocaleNumber(messagesInQueue)"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                        <i class="fas fa-stream text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Messages Processed') }}</h5>
                                    <span class="h2 font-weight-bold mb-0"
                                        x-text="__Utils.formatAsLocaleNumber(totalMessagesProcessed)"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                        <i class="fas fa-tasks text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- show.dropdown.result --}}
@elseif(hasVendorAccess() or hasVendorUserAccess() or $vendorViewBySuperAdmin )
<div class="header">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-12">
                    <div class="row mb-2">
                        @if (hasVendorAccess('manage_contacts'))
                        {{-- total contacts --}}
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-md-4">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Contacts') }}</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ __tr($totalContacts) }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div
                                                class="icon icon-shape bg-info text-white rounded-circle shadow">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @if(!$vendorViewBySuperAdmin)
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <a href="{{ route('vendor.contact.read.list_view') }}">{{  __tr('Manage Contacts') }}</a>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- /total contacts --}}
                        {{-- total groups --}}
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-md-4">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Groups') }}</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ __tr($totalGroups) }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div
                                                class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @if(!$vendorViewBySuperAdmin)
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <a href="{{ route('vendor.contact.group.read.list_view') }}">{{  __tr('Manage Groups') }}</a>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- /total groups --}}
                        @endif
                        @if (hasVendorAccess('manage_campaigns'))
                        {{-- total totalCampaigns --}}
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-md-4">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Campaigns') }}</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ __tr($totalCampaigns) }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div
                                                class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                                <i class="fa fa-bullhorn"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @if(!$vendorViewBySuperAdmin)
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <a href="{{ route('vendor.campaign.read.list_view') }}">{{  __tr('Manage Campaigns') }}</a>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- /total totalCampaigns --}}
                        @endif
                        @if (hasVendorAccess('manage_templates'))
                        {{-- total totalTemplates --}}
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-md-4">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Templates') }}</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ __tr($totalTemplates) }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div
                                                class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                                <i class="fa fa-layer-group"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @if(!$vendorViewBySuperAdmin)
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <a href="{{ route('vendor.whatsapp_service.templates.read.list_view') }}">{{  __tr('Manage Templates') }}</a>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- /total totalTemplates --}}
                        @endif
                        @if (hasVendorAccess('manage_bot_replies'))
                        {{-- total totalBotReplies --}}
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-md-4">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Bot Replies') }}</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ __tr($totalBotReplies) }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div
                                                class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                                <i class="fa fa-robot"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @if(!$vendorViewBySuperAdmin)
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <a href="{{ route('vendor.bot_reply.read.list_view') }}">{{  __tr('Manage Bot Replies') }}</a>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- /total totalBotReplies --}}
                        @endif
                          {{-- total active team member --}}
                          @if (hasVendorAccess('administrative'))
                          <div class="col-xl-3 col-lg-4 col-md-6 mb-md-4">
                             <div class="card card-stats mb-4 mb-xl-0">
                                 <div class="card-body">
                                     <div class="row">
                                         <div class="col">
                                             <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Active Team Members') }}</h5>
                                             <span class="h2 font-weight-bold mb-0">{{ __tr($activeTeamMembers) }}</span>
                                         </div>
                                         <div class="col-auto">
                                             <div
                                                 class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                                 <i class="fas fa-user-tie"></i>
                                             </div>
                                         </div>
                                     </div>
                                     @if(!$vendorViewBySuperAdmin)
                                     <p class="mt-3 mb-0 text-muted text-sm">
                                         <a href="{{ route('vendor.user.read.list_view') }}">{{  __tr('Manage Team Member') }}</a>
                                     </p>
                                     @endif
                                 </div>
                             </div>
                         </div>
                         @endif
                         {{-- /total active team member --}}
                          {{-- manage campaigns --}}
                        @if (hasVendorAccess('manage_campaigns'))
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-md-4">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Messages in Queue') }}</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ __tr($messagesInQueue) }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                                <i class="fas fa-stream text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- /manage campaigns --}}
                         {{-- Messaging Processed--}}
                        @if (hasVendorAccess('messaging'))
                        <div class="col-md-6 col-lg col-sm-12">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Messages Processed') }}</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ __tr($totalMessagesProcessed) }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                                <i class="fas fa-tasks text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                         {{-- /Messaging Processed --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif