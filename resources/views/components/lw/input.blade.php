@props([
'label' => null
])

@if($label)
<label class="block font-medium text-sm text-gray-700">
    {{ $label }}
    <input {!! $attributes->merge(['class' => 'form-input rounded-md shadow-sm', 'type' => "text"]) !!}>
</label>
@else
<input {!! $attributes->merge(['class' => 'form-input rounded-md shadow-sm', 'type' => "text"]) !!}>
@endif