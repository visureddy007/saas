@php
/**
* File          : subscription-plans.blade.php
----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => ($pageTitle ?? '')])
@section('content')
    @include('users.partials.header', [
    'title' => __tr('Subscription Plans') . ' '. auth()->user()->name,
    'description' => '',
    'class' => 'col-lg-12'
    ])
    <!-- Start of Page Wrapper -->
    <div class="container-fluid accordion" id="lwSubscriptionPlansContainer">
        <div class="row">
            {{-- Free Plan --}}
            <div class="col-xl-12 mb-5">
                <div class="card">
                    <h3 class="card-header" data-toggle="collapse" data-target="#lwFreePlanBlock" aria-expanded="true"
                    aria-controls="lwFreePlanBlock">
                        {{ __tr('Free Plan Configurations') }}
                    </h3>
                    <div class="card-body collapse" id="lwFreePlanBlock" data-parent="#lwSubscriptionPlansContainer">
                        @php
                            $freeFeatures = $freePlan['features'];
                        @endphp
                        <!--  Add New Plan Form -->
                        <x-lw.form id="lwAddNewPlanForm-Free"
                            :action="route('manage.configuration.subscription-plans.write.update')">
                            <!-- form fields form fields -->
                            <input type="hidden" name="config_plan_id" value="free">
                            <x-lw.checkbox data-lw-plugin="lwSwitchery" id="selectFreePlan" name="enabled" :checked="$freePlan['enabled']" data-lw-plugin="lwSwitchery"
                                :label="__tr('Enable this Plan')" />
                            <hr class="my-3">
                            <!-- Title -->
                            <x-lw.input-field type="text" id="free_title" name="title" :label="__tr('Title')" required
                                placeholder="{{ __tr('your plan title') }}"
                                value="{{ strtr($freePlan['title'] ?? '', ['__title__' => '']) ?? '' }}" />
                            <div class="">
                                <h3 class="text-danger mt-4">{{ __tr('Feature Limits') }}</h3>
                                <div class="row pl-3">
                                    @if (!__isEmpty($freeFeatures))
                                        @foreach ($freeFeatures as $featureKey => $feature)
                                        @php
                                            $structureFeature = $freePlanStructure['features'][$featureKey];
                                        @endphp
                                            <fieldset class="col-xl-3 mr-4 float-left">
                                                <legend>{{ $structureFeature['description'] }}</legend>
                                                <!-- Description -->
                                                @if (isset($structureFeature['type']) and ($structureFeature['type'] == 'switch'))
                                                <input type="hidden" name="{{ $featureKey }}_limit" value="0">
                                                <x-lw.checkbox id="free_{{ $featureKey }}_limit" name="{{ $featureKey }}_limit" data-lw-plugin="lwSwitchery" :checked="$feature['limit']" :label="__tr('Enable __itemDescription__', [
                                                    '__itemDescription__' => $structureFeature['description']
                                                ])" value="1" />
                                                @else
                                                <!-- limit -->
                                                <x-lw.input-field :appendText="$structureFeature['limit_duration_title'] ?? ''" type="number" id="free_{{ $featureKey }}_limit"
                                                    name="{{ $featureKey }}_limit" :label="$structureFeature['description']"
                                                    required min="-1" placeholder="{{ $feature['description'] }}"
                                                    value="{{ is_numeric($feature['limit'] ?? '') ? $feature['limit'] : '' }}"
                                                    :helpText="__tr('Use -1 for unlimited')" />
                                                    @endif
                                            </fieldset>
                                        @endforeach
                                    @endif
                                </div>

                            </div>
                            <div class="mt-5">
                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary">{{ __tr('Update') }}</button>
                            </div>
                            <!-- form footer -->
                        </x-lw.form>
                        <!--/  Add New Plan Form -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($planStructure as $planKey => $plan)
                @php
                    $savedPlan = $planDetails[$planKey];
                    $planId = $plan['id'];
                    $features = $plan['features'];
                @endphp
                <div class="col-xl-12 mb-5">
                    <div class="card">
                        <h3 class="card-header" data-toggle="collapse" data-target="#lwPaidBlock{{ $planId }}" aria-expanded="true"
                        aria-controls="lwPaidBlock{{ $planId }}">
                            #{{ $planId }} {{ __tr('Plan Configurations') }}
                        </h3>
                        <div class="card-body collapse" id="lwPaidBlock{{ $planId }}" data-parent="#lwSubscriptionPlansContainer">
                            <!--  Add New Plan Form -->
                            <x-lw.form id="lwAddNewPlanForm-{{ $planId }}"
                                :action="route('manage.configuration.subscription-plans.write.update')">
                                <!-- form fields form fields -->
                                <input type="hidden" name="config_plan_id" value="{{ $planId }}">
                                <x-lw.checkbox id="select_{{ $planId }}" name="enabled" :checked="$savedPlan['enabled']"
                                data-lw-plugin="lwSwitchery"
                                    :label="__tr('Enable this Plan')" />
                                <hr class="my-3">
                                <!-- Title -->
                                <x-lw.input-field type="text" id="{{ $planId }}_title" name="title"
                                    :label="__tr('Title')" required placeholder="{{ __tr('your plan title') }}"
                                    value="{{ strtr($savedPlan['title'] ?? $plan['title'] ?? '', ['__title__' => '']) ?? '' }}" />
                                <h3 class="text-danger mt-4">{{ __tr('Feature Limits') }}</h3>
                                <div class="row pl-3">
                                    @if (!__isEmpty($features))
                                        @foreach ($features as $featureKey => $feature)
                                            @php
                                                $structureFeature = $feature;
                                                $feature = $savedPlan['features'][$featureKey];
                                            @endphp
                                            <fieldset class="col-xl-3 mr-4 float-left">
                                                <legend>{{ $structureFeature['description'] }}</legend>
                                                <!-- Description -->
                                                @if (isset($structureFeature['type']) and ($structureFeature['type'] == 'switch'))
                                                <input type="hidden" name="{{ $featureKey }}_limit" value="0">
                                                <x-lw.checkbox id="{{ $planId }}_{{ $featureKey }}_limit" name="{{ $featureKey }}_limit" data-lw-plugin="lwSwitchery" :checked="$feature['limit']" :label="__tr('Enable __itemDescription__', [
                                                    '__itemDescription__' => $structureFeature['description']
                                                ])" value="1" />
                                                @else
                                                <x-lw.input-field :appendText="$structureFeature['limit_duration_title'] ?? ''" type="number"
                                                    id="{{ $planId }}_{{ $featureKey }}_limit"
                                                    name="{{ $featureKey }}_limit" :label="$structureFeature['description']"
                                                    required min="-1" placeholder="{{ $feature['description'] }}"
                                                    value="{{ is_numeric($feature['limit'] ?? '') ? $feature['limit'] : '' }}"
                                                    :helpText="__tr('Use -1 for unlimited')" />
                                                    @endif
                                            </fieldset>
                                        @endforeach
                                    @endif

                                </div>
                                <h3 class="text-danger mt-4">{{ __tr('Charges') }}</h3>
                                @if (!__isEmpty($plan['charges']))
                                    <div class="row">
                                        <div class="col-xl-12">
                                            @foreach ($plan['charges'] as $itemKey => $itemValue)
                                                @php
                                                    $itemValue = $planDetails[$planKey]['charges'][$itemKey];
                                                @endphp
                                                <fieldset class="col-xl-3 float-left mr-4">
                                                    <legend>{{ $itemKey }}</legend>
                                                    <x-lw.checkbox id="select_{{ $planId }}_{{ $itemKey }}"
                                                        name="{{ $itemKey }}_enabled" :checked="$itemValue['enabled']"
                                                        data-lw-plugin="lwSwitchery"
                                                        :label="__tr('Enable this Charge')" />
                                                    <hr class="my-2">
                                                    <x-lw.input-field type="text"
                                                        id="{{ $planId }}_{{ $itemKey }}_plan_price_id"
                                                        name="{{ $itemKey }}_plan_price_id"
                                                        :label="__tr('Stripe Plan Price ID')"
                                                        placeholder="{{ __tr('stripe plan price id') }}"
                                                        value="{{ $itemValue['price_id'] }}" />
                                                    <x-lw.input-field type="number"
                                                        id="{{ $planId }}_{{ $itemKey }}_charge"
                                                        name="{{ $itemKey }}_charge" :label="__tr('Charge Amount')"
                                                        min="0" placeholder="{{ __tr('Charge Amount') }}"
                                                        value="{{ $itemValue['charge'] }}">
                                                        <x-slot name="prependText">
                                                            {{ getCurrencySymbol() }}
                                                        </x-slot>
                                                        <x-slot name="appendText">
                                                            {{ getCurrency() }}
                                                        </x-slot>
                                                    </x-lw.input-field>
                                                </fieldset>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="text-left mt-3 pb-3">
                                    <input type="hidden" name="charges">
                                </div>
                                <div class="mt-5">
                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-primary">{{ __tr('Update') }}</button>
                                </div>
                                <!-- form footer -->
                            </x-lw.form>
                            <!--/  Add New Plan Form -->
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
