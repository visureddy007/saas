@props(['value', 'dataCallbackParams' => '', 'dataOnCloseUpdateModels' => '', 'action' => null])
@php
if (!Illuminate\Support\Str::contains($action, [
    'http://',
    'https://'
])) {
if ($action) {
    $action = route($action);
}
}
$additionalAttributes = [];
if ($dataCallbackParams) {
    $additionalAttributes['data-callback-params'] = json_encode($dataCallbackParams);
}

if ($dataOnCloseUpdateModels) {
    $additionalAttributes['data-on-close-update-models'] = json_encode($dataOnCloseUpdateModels);
}
$ajaxFormClass = 'lw-ajax-form';
if ($attributes->get('data-ajax') === 'false') {
$ajaxFormClass = '';
}
@endphp
<form @if ($action) action="{{ $action }}" @endif
    {{ $attributes->merge(
    array_merge(
        [
            'class' => 'lw-form ' . $ajaxFormClass,
            'method' => 'POST',
            // 'data-secured' => "false",
            'data-show-processing' => 'true',
            'role' => 'form',
            'data-error-class' => 'has-danger',
        ],
        $additionalAttributes,
    ),
) }} novalidate>
    @csrf
    {{ $slot }}
    <div class="lw-form-overlay"></div>
</form>
