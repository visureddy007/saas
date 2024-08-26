<div class="lw-whatsapp-preview-message-container">
    <div class="lw-whatsapp-preview">
        <div class="card ">
            <div class="lw-whatsapp-header-placeholder ">
                @if ($mediaValues['type'] == 'video')
                <video class="lw-whatsapp-header-video" controls src="{{ $mediaValues['link'] }}"></video>
                @elseif ($mediaValues['type'] == 'audio')
                <audio class="lw-whatsapp-header-audio my-auto mx-4" controls>
                    <source src="{{ $mediaValues['link'] }}" type="{{ $mediaValues['mime_type'] ?? null }}">
                  {{  __tr('Your browser does not support the audio element.') }}
                  </audio>
                @elseif ($mediaValues['type'] == 'image')
                <a class="lw-wa-message-document-link" target="_blank" href="{{ $mediaValues['link'] }}"><img class="lw-whatsapp-header-image" src="{{ $mediaValues['link'] }}" alt=""></a>
                @elseif ($mediaValues['type'] == 'document')
                <a class="lw-wa-message-document-link" title="{{ __tr('Document Link') }}" target="_blank" href="{{ $mediaValues['link'] }}"><i class="fa fa-5x fa-file-alt text-white"></i></a>
                @endif
            </div>
            @isset($mediaValues['caption'])
            <div class="p-2 lw-plain-message-text">{!! $mediaValues['caption'] !!}</div>
            @endisset
        </div>
    </div>
</div>