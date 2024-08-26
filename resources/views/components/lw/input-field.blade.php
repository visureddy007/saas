{{-- Has a support for
    text, number, selectize (use selectOptions slot) --}}
    @props([
        'label' => null,
        'helpText' => null,
        'prepend' => null,
        'append' => null,
        'appendText' => null,
        'prependText' => null,
    ])
    @php
    $id = $attributes->get('id');
    $dataType = $attributes->get('type');
    $formGroupClass = $attributes->get('data-form-group-class') ?? '';
    $inputGroupClass = $attributes->get('data-input-group-class') ?? '';
    if (!$id) {
        $id = sha1(json_encode($attributes->getAttributes()));
    }
    @endphp
    <div class="form-group mb-1 {{ $formGroupClass }}">
        <label for="{{ $id }}"><?= $label ?></label>
        @if ($append or $appendText or $prepend or $prependText)
            <div class="input-group {{ $inputGroupClass }}">
                @if ($prepend)
                    <div class="input-group-prepend">
                        {!! $prepend !!}
                    </div>
                @endif
                @if ($prependText)
                <div class="input-group-prepend">
                    <span class="input-group-text">{!! $prependText !!}</span>
                </div>
            @endif
                <input {!! $attributes->merge(['class' => 'lw-form-field form-control', 'type' => 'text', 'id' => $id]) !!} />
                @if ($append)
                    <div class="input-group-append">
                        {!! $append !!}
                    </div>
                @endif
                @if ($appendText)
                    <div class="input-group-append">
                        <span class="input-group-text">{!! $appendText !!}</span>
                    </div>
                @endif
            </div>
        @elseif(($dataType === 'selectize') or ($dataType === 'select'))
            @if ($dataType === 'select')
                <select {{ $dataType }} {!! $attributes->merge(['class' => 'lw-form-field form-control', 'id' => $id]) !!}>
                    {!! $selectOptions !!}
                </select>
            @elseif($dataType === 'selectize')
                <select data-lw-plugin="lwSelectize" {!! $attributes->merge(['class' => 'lw-form-field form-control', 'id' => $id]) !!}>
                    {!! $selectOptions !!}
                </select>
            @endif
        @else
            <input {!! $attributes->merge(['class' => 'lw-form-field form-control', 'type' => 'text', 'id' => $id]) !!} />
        @endif
        @if ($helpText)
            <span class="form-text text-muted mt-3 text-sm">{{ $helpText }}</span>
        @endif
    </div>