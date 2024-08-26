@php
$iterationIndex = 0;
@endphp
<div class="row">
    @foreach ($parameters as $parameter)
@php
$parameterIndex = strtr($parameter, [
        'field_' => '',
        'button_' => '',
]);
@endphp
<div class="col-md-12 col-lg-6 card border-0">
    @if ($subjectType == 'button')
        @isset($buttonItems[$iterationIndex])
            @if($buttonItems[$iterationIndex]['type'] == 'URL')
            {{ $buttonItems[$iterationIndex]['text'] }}  - {{ $buttonItems[$iterationIndex]['url'] }}
            @elseif($buttonItems[$iterationIndex]['type'] == 'COPY_CODE')
            {{ $buttonItems[$iterationIndex]['text'] }}
            @endif
        @endisset
    @endif
    <x-lw.input-field  placeholder="{{  __tr('Choose or Write you own') }}" type="selectize" data-lw-plugin="lwSelectize" id="lwField_{{ $parameter }}"
        name="{{ $parameter }}" data-form-group-class="" data-selected=" " :label="is_numeric( $parameterIndex) ? __tr('Assign content for @{{__messageParameter__}} variable', [
                '__messageParameter__' => '<strong>'. ($subjectType == 'button' ? '1' : Str::of(Str::title($parameterIndex))->replace(
    '_', ' '
)) .'</strong>'
            ]) : __tr('Assign content for __messageParameter__ variable', [
                '__messageParameter__' => '<strong>'. ($subjectType == 'button' ? '1' : Str::of(Str::title($parameterIndex))->replace(
    '_', ' '
)) .'</strong>'
            ])"  data-create="true">
        <x-slot name="selectOptions">
            <option value="">{{ __tr('Select or Type your own') }}</option>
            <optgroup label="{{ __tr('Assign from User Contact Details') }}">
                @foreach ($contactDataMaps as $contactDataMapKey => $contactDataMapValue)
                    <option value="{{ $contactDataMapKey }}">{{ $contactDataMapValue }}</option>
                @endforeach
            </optgroup>
            <optgroup label="{{ __tr('or custom values') }}">
                <option disabled="">{{  __tr('type to use custom value') }}</option>
            </optgroup>
        </x-slot>
    </x-lw.input-field>
</div>
@endforeach
</div>