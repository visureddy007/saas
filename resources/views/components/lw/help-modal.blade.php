@props([
'subject' => '',
])
{{-- Modal Trigger --}}
<button type="button" class="lw-btn btn btn-info" data-toggle="modal" data-target="#lwHelpDialog_{{ Str::slug($subject) }}">{{  __tr('Help!') }}</button>
<!-- Modal -->
<div data-backdrop="static" tabindex="-1" aria-labelledby="{{ $subject }}" aria-hidden="true" class="modal fade" id="lwHelpDialog_{{ Str::slug($subject) }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ $subject }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?= __tr('Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>