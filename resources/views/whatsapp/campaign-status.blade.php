@extends('layouts.app', ['title' => __tr('Campaign Status')])
@section('content')
@include('users.partials.header', [
'title' => __tr('Campaign Dashboard'),
'description' => '',
'class' => 'col-lg-7'
])
@php
$campaignData = $campaign->__data;
$selectedGroups = Arr::get($campaignData, 'selected_groups', []);
$isRestrictByTemplateContactLanguage = Arr::get($campaignData, 'is_for_template_language_only');
$isAllContacts = Arr::get($campaignData, 'is_all_contacts');
$messageLog = $campaign->messageLog;
$queueMessages = $campaign->queueMessages;
$campaignUid=$campaign->_uid;
@endphp

<div class="container-fluid mt-lg--6 lw-campaign-window-{{ $campaign->_uid }}" x-cloak x-data="initialRequiredData">
    <div class="row">
        <!-- button -->
        <div class="col-12 mb-3">
            <div class="float-right">
                <a class="lw-btn btn btn-secondary" href="{{ route('vendor.campaign.read.list_view') }}">{{ __tr('Back to Campaigns') }}</a>
                <a class="lw-btn btn btn-primary" href="{{ route('vendor.campaign.new.view') }}">{{ __tr('Create New Campaign') }}</a>
            </div>
        </div>
        <!--/ button -->
        <div class="col-12 mb-4 ">
            <div class="card card-stats mb-4 mb-xl-0 ">
                <div class="card-body ">
                    <div class="row">
                        <div class="col">
                            @if($campaign->status == 5) <span class="rounded py-1 px-3 badge-dark text-white mb-2 float-right">{{  __tr('Archived') }}</span> @endif
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Campaign Name') }}</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $campaign->title }}</span>
                            <p class="mt-3 mb-0 text-muted text-sm">
                            <h2 class="badge badge-warning fs-2" x-text="statusText"></h2>
                            <h3 class="text-success mr-2">{{ __tr('Execution Scheduled at') }}</h3>
                            @if ($campaign->scheduled_at > now())
                            <div class="text-warning">{{ formatDiffForHumans($campaign->scheduled_at, 3) }}</div>
                            @else
                            <template x-if="(executedCount == 0) && inQueuedCount">
                                <div class="text-warning my-3">
                                    {{  __tr('Awaiting execution') }} <i class="fa fa-spin fa-spinner"></i>
                                </div>
                            </template>
                            @endif
                            @if ($campaign->timezone and getVendorSettings('timezone') != $campaign->timezone)
                            <div class="">{!! __tr('__scheduledAt__ as per your account timezone which is __selectedTimezone__', [
                                '__scheduledAt__' => formatDateTime($campaign->scheduled_at),
                                '__selectedTimezone__' => '<strong>'. getVendorSettings('timezone') .'</strong>'
                                ]) !!} </div>
                            <div class=" text-muted">{!! __tr('Campaign scheduled on __scheduledAt__ as per the __selectedTimezone__ timezone', [
                                '__scheduledAt__' => formatDateTime($campaign->scheduled_at_by_timezone, null, null,
                                $campaign->timezone),
                                '__selectedTimezone__' => '<strong>'. $campaign->timezone .'</strong>'
                                ]) !!}</div>
                            @else
                            <span class="text-nowrap">{{ formatDateTime($campaign->scheduled_at) }}</span>
                            @endif

                            </p>
                            <div class="my-3">
                                <h5 class="card-title text-uppercase text-muted mb-2">{{ __tr('template Name') }}</h5>
                                <span class="h3 font-weight-bold mb-2">{{ $campaign->template_name }}</span>
                                <h5 class="card-title text-uppercase text-muted mb-2 mt-3">{{ __tr('template language')
                                    }}
                                </h5>
                                <span class="h3 font-weight-bold mb-2">{{ $campaign->template_language }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row mb-4">
                {{-- total contacts --}}
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Contacts') }}
                                    </h5>
                                    <span class="h2 font-weight-bold mb-0" x-text="totalContacts"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-muted text-sm">
                                @if ($isAllContacts)
                                {{ __tr('All contacts ') }}
                                @else
                                {{ __tr('All contacts from: ') }}
                                @foreach ($selectedGroups as $selectedGroup)
                                <strong class="text-nowrap text-warning">{{ $selectedGroup['title'] }}</strong>
                                @endforeach
                                {{ __tr(' groups.') }}
                                @endif
                                @if ($isRestrictByTemplateContactLanguage)
                                <span class="">{!! __tr('Excluding those contacts which don\'t have __languageCode__
                                    language', [
                                    '__languageCode__' => "<span class='text-warning'>". e($campaign->template_language)
                                        ."</span>"
                                    ]) !!}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                {{-- /total contacts --}}
                {{-- delivered to --}}
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Delivered') }}
                                    </h5>
                                    <span class="h2 font-weight-bold mb-0" x-text="totalDeliveredInPercent"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="text-nowrap" x-text="__Utils.formatAsLocaleNumber(totalDelivered)"></span>
                                <span class="text-nowrap">{{ __tr('Contacts') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                {{-- /delivered to --}}
                {{-- read by --}}
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Read') }}</h5>
                                    <span class="h2 font-weight-bold mb-0" x-text="totalReadInPercent"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-check-double"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="text-nowrap" x-text="__Utils.formatAsLocaleNumber(totalRead)"></span>
                                <span class="text-nowrap">{{ __tr('Contacts') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                {{-- /read by --}}
                {{-- failed --}}
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __tr('Total Failed') }}
                                    </h5>
                                    <span class="h2 font-weight-bold mb-0" x-text="__Utils.formatAsLocaleNumber(totalFailedInPercent)"></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="text-nowrap" x-text="__Utils.formatAsLocaleNumber(totalFailed)"></span>
                                <span class="text-nowrap">{{ __tr('Contacts') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                {{-- /failed --}}
            </div>
            {{-- message log --}}
              <!--start of tabs-->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $pageType == "queue" ? 'active' : '' ?>"
                   href="<?= route('vendor.campaign.status.view', ['campaignUid' => $campaignUid, 'pageType' => 'queue']) ?>#logData">
                    <?= __tr('Queue') ?>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $pageType == "executed" ? 'active' : '' ?>"
                   href="<?= route('vendor.campaign.status.view', ['campaignUid' => $campaignUid, 'pageType' => 'executed']) ?>#logData">
                    <?= __tr('Executed') ?>
                </a>
            </li>
            <li class="nav-item text-right col">
                @if (($pageType == "queue") and ($campaign->status == 1))
                <template x-if="campaignStatus == 'executed' && (queueFailedCount > 0)">
                <a class="btn btn-warning btn-sm lw-ajax-link-action" data-confirm="#requeueFailedMessageConfirm-template" data-method="post" href="{{ route('vendor.campaign.requeue.log.write.failed', [
                    'campaignUid' => $campaignUid
                ]) }}"><i class="fa fa-redo-alt"></i> {{  __tr('Requeue Failed Message') }}</a>
                 </template>
                @endif
                <button @click="window.reloadDT('#lwCampaignQueueLog');" class="btn btn-dark btn-sm"><i class="fa fa-sync"></i> {{  __tr('Refresh') }}</button>
             @if($campaignStatus=="executed")
             @if($pageType== "queue")
             <a href="{{ route('vendor.campaign.queue.log.report.write',['exportType' => 'data','campaignUid' => $campaignUid ] 
             ) }}" data-method="post" class="btn btn-dark btn-sm"><i class="fa fa-download"></i> {{  __tr('Report') }}</a>
             @elseif($pageType== "executed")
                 <a href="{{ route('vendor.campaign.executed.report.write',['exportType' => 'data','campaignUid' => $campaignUid ] 
                     ) }}" data-method="post" class="btn btn-dark btn-sm"><i class="fa fa-download"></i> {{  __tr('Report') }}</a>
                     @endif
                @endif
                {{-- </template> --}}
            </li>
        </ul>
        <!--/end of tabs -->
        <!-- tab Container -->
        <div class="row">
            <div class="col-12 mb-4 " id="logData">
                @if($pageType== "queue")
                    @include('whatsapp.campaign-queue-log-partial')
                    @elseif($pageType== "executed")
                    @include('whatsapp.campaign-executed-log-partial')
                    @endif
            </div>
        </div>
        <script type="text/template" id="requeueFailedMessageConfirm-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want requeue all the failed messages to process it again?') }}</p>
    </script>
        </div>
    </div>
</div>
@php
$totalContacts = (int) Arr::get($campaignData, 'total_contacts');
$totalRead = $messageLog->where('status', 'read')->count();
$totalReadInPercent = round($totalRead / $totalContacts * 100, 2) . '%';
$totalDelivered = $messageLog->where('status', 'delivered')->count();
$totalDeliveredInPercent = round(($totalDelivered + $totalRead) / $totalContacts * 100, 2) . '%';
$totalFailed = $queueMessages->where('status', 2)->count() + $messageLog->where('status', 'failed')->count();
$totalFailedInPercent = round($totalFailed / $totalContacts * 100, 2) . '%';
@endphp
<script>
    (function() {
        'use strict';
        document.addEventListener('alpine:init', () => {
            Alpine.data('initialRequiredData', () => ({
                totalContacts:'{{ __tr($totalContacts)  }}',
                totalDeliveredInPercent:'{{ __tr($totalDeliveredInPercent) }}',
                totalDelivered:'{{ __tr($totalDelivered + $totalRead) }}',
                totalRead:'{{ __tr($totalRead) }}',
                totalReadInPercent:'{{ __tr($totalReadInPercent) }}',
                totalFailed:'{{ __tr($totalFailed) }}',
                totalFailedInPercent:'{{ __tr($totalFailedInPercent) }}',
                executedCount:{{ $messageLog->count() ?? 0 }},
                inQueuedCount:{{ $queueMessages->where('status', 1)->count() ?? 0 }},
                statusText:'{{ $statusText }}',
                campaignStatus:'{{ $campaignStatus }}',
                queueFailedCount:{{ $queueFailedCount }},
            }));
        });
    })();
</script>
@push('appScripts')
<script>
(function($) {
    'use strict';
    // initial request
    __DataRequest.get("{{ route('vendor.campaign.status.data', ['campaignUid' => $campaignUid ]) }}");
})();
</script>
@endpush
@endsection()