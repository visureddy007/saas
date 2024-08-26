@props([
'url' => null,
'header' => ''
])
@php
$cardClasses = $attributes->get('lw-card-classes') ?? '';
if (!Illuminate\Support\Str::contains($url, ['http://', 'https://'])) {
    if($url) {
        $url = route($url);
    }
}
@endphp
<div class="card shadow {{ $cardClasses }}">
    @if($header)
    <h2 class="card-header">
        {{ $header }}
    </h2>
    @endif
    <div class="card-body">
        <div class="">
            <table lwDataTable @if($url) data-url="{{ $url }}" @endif {{ $attributes->merge(['class' => 'table table-striped']) }}>
                <thead>
                    <tr>
                        {{ $slot }}
                    </tr>
                </thead>
                <tbody ></tbody>
            </table>
        </div>
    </div>
</div>