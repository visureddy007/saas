@props([
    'header' => '',
    'hasForm' => false,
    'modalSize' => 'modal-lg',
])
@php
$modalDialogClass = $attributes->get('modal-dialog-class') ?? '';
@endphp
<!-- Modal -->
<div data-backdrop="static" tabindex="-1" aria-labelledby="{!! $header !!}" aria-hidden="true"
    {{ $attributes->merge(['class' => 'modal fade ' . ($hasForm ? 'lw-has-form' : '')]) }}>
    <div class="modal-dialog {{ $modalSize }} {{ $modalDialogClass }}">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{!! $header !!}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body {{ $hasForm ? 'p-0' : '' }}">
                {{ $slot }}
            </div>
            @if (!$hasForm)
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __tr('Close') ?></button>
          {{ $footer ?? '' }}
        </div>
        @endif
      </div>
    </div>
  </div>
