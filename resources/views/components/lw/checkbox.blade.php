@props([
'disabled' => false,
'label' => null,
'checked' => false,
'name' => $attributes->get('name'),
'id' => $attributes->get('name'),
'offValue' => null,
])
@if($label)
<label for="{{ $id }}" class="flex items-center">
    @if($offValue !== null)
    <input type="hidden" name="{{ $name }}" value="{{ $offValue }}">
    @endif
    <input id="{{ $id }}" {{ $disabled ? 'disabled' : '' }} {{ $checked ? 'checked' : '' }} type="checkbox" name="{{ $name }}" {!! $attributes->merge(['class' => 'form-checkbox']) !!}>
    <span class="ml-2 text-gray-600">{{ $label }}</span>
</label>
@else
<input id="{{ $id }}" {{ $disabled ? 'disabled' : '' }} {{ $checked ? 'checked' : '' }} type="checkbox" name="{{ $name }}" {!! $attributes->merge(['class' => 'form-checkbox']) !!}>
@endif