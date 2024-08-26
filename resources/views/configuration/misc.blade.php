<!-- Page Heading -->
@php
$availableHomePages = [
    'outer-home' => __tr('Home Page 1'),
    'outer-home-2' => __tr('Home Page 2'),
];
@endphp
<section>
    <h1>{!! __tr('Misc Setting') !!}</h1>
 <!-- /Select Default language -->
    <fieldset x-data="{panelOpened:false}" x-cloak>
        <legend @click="panelOpened = !panelOpened">{{ __tr('Home Page Settings') }} <small class="text-muted">{{  __tr('Click to expand/collapse') }}</small></legend>
        <form x-show="panelOpened" class="lw-ajax-form lw-form" method="post" action="<?= route('manage.configuration.write', ['pageType' => 'misc_settings']) ?>">
             <!-- Select home page  -->
            <x-lw.input-field type="selectize" data-form-group-class="col-md-4" name="current_home_page_view" data-selected="{{ getAppSettings('current_home_page_view') }}"
     :label="__tr('Select home page')" placeholder="{{ __tr('Select home page') }}" required>
     <x-slot name="selectOptions">
        @foreach ($availableHomePages as $availableHomePageKey => $availableHomePage)
            <option value="{{ $availableHomePageKey }}">{{ $availableHomePage }}</option>
        @endforeach
     </x-slot>
 </x-lw.input-field>
  <!-- /Select home page  -->
 <h3 class="my-5 col-md-4 text-center text-muted">{{  __tr('------- OR -------') }}</h3>
        <div class="mb-3 mb-sm-0 col-md-4">
            <label id="lwOtherHomePage">{{  __tr('External Home page') }} </label>
            <div class="form-group">
                <label id="lwOtherHomePageUrl">{{  __tr('Set home page url if you want to use other home page than default') }} </label>
                <input type="url" class="form-control" id="lwOtherHomePageUrl" name="other_home_page_url" value='{{ getAppSettings('other_home_page_url') }}'>
            </div>
        </div>
        <hr>
        <div class="form-group col" name="footer_code">
            <button type="submit" class="btn btn-primary btn-user lw-btn-block-mobile">{{ __tr('Save') }}</button>
        </div>
    </form>
    </fieldset>
</section>