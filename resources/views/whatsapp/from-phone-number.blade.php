{{-- From Phone Number ID --}}
<x-lw.input-field type="selectize" data-form-group-class="col-sm-12 col-md-4 col-lg-6 p-0" name="from_phone_number_id" :label="__tr('Send using Phone Number')" data-selected="{{ getVendorSettings('current_phone_number_id') }}">
    <x-slot name="selectOptions">
        @if(!empty(getVendorSettings('whatsapp_phone_numbers')))
        @foreach (getVendorSettings('whatsapp_phone_numbers') as $whatsappPhoneNumber)
        <option value="{{ $whatsappPhoneNumber['id'] }}">{{ $whatsappPhoneNumber['display_phone_number'] }}</option>
        @endforeach
        @elseif(getVendorSettings('current_phone_number_id'))
        <option value="{{ getVendorSettings('current_phone_number_id') }}">{{ getVendorSettings('current_phone_number_number') }}</option>
        @endif
    </x-slot>
</x-lw.input-field>
{{-- /From Phone Number ID --}}