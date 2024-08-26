<div class="lw-whatsapp-preview-message-container">
    <div class="lw-whatsapp-preview">
        <div class="card ">
            @if ($mediaValues['header_type'] and ($mediaValues['header_type'] != 'text'))
            <div class="lw-whatsapp-header-placeholder ">
                @if ($mediaValues['header_type'] == 'video')
                <video class="lw-whatsapp-header-video" controls src="{{ $mediaValues['media_link'] }}"></video>
                @elseif ($mediaValues['header_type'] == 'audio')
                <audio class="lw-whatsapp-header-audio my-auto mx-4" controls>
                    <source src="{{ $mediaValues['media_link'] }}">
                  {{  __tr('Your browser does not support the audio element.') }}
                  </audio>
                @elseif ($mediaValues['header_type'] == 'image')
                <a class="lw-wa-message-document-link" target="_blank" href="{{ $mediaValues['media_link'] }}"><img class="lw-whatsapp-header-image" src="{{ $mediaValues['media_link'] }}" alt=""></a>
                @elseif ($mediaValues['header_type'] == 'document')
                <a class="lw-wa-message-document-link" title="{{ __tr('Document Link') }}" target="_blank" href="{{ $mediaValues['media_link'] }}"><i class="fa fa-5x fa-file-alt text-white"></i></a>
                @endif
            </div>
            @endif
            <div class="lw-whatsapp-body">
            @isset($mediaValues['header_text'])
            <strong class="mb-2 d-block">{{ $mediaValues['header_text'] }}</strong>
            @endisset
            @isset($mediaValues['body_text'])
            <div>{{ $mediaValues['body_text'] }}</div>
            @endisset
        </div>
            @isset($mediaValues['footer_text'])
            <div class="lw-whatsapp-footer text-muted">{{ $mediaValues['footer_text'] }}</div>
            @endisset
            @isset($mediaValues['buttons'])
            <div class="card-footer lw-whatsapp-buttons">
                <div class="list-group list-group-flush lw-whatsapp-buttons">
                    @foreach ($mediaValues['buttons'] as $button)
                    <div class="list-group-item">
                        <i class="fa fa-reply"></i> {{ $button }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endisset
            @if(isset($mediaValues['cta_url']) and !empty($mediaValues['cta_url']))
            <div class="card-footer lw-whatsapp-buttons">
                <div class="list-group list-group-flush lw-whatsapp-buttons">
                    <a href="{{ $mediaValues['cta_url']['url'] ?? '' }}" target="_blank" class="list-group-item">
                        <i class="fas fa-external-link-alt"></i> {{ $mediaValues['cta_url']['display_text'] ?? '' }}
                    </a>
                </div>
            </div>
            @endisset
            @isset($mediaValues['list_data']['sections'])
            <div x-data="{showBottomSheet:false}">
                <button class="btn btn-light btn-block btn-sm" @click="showBottomSheet = !showBottomSheet"> <i class="fa fa-list"></i> {{ $mediaValues['list_data']['button_text'] }}</button>
            <div x-show="showBottomSheet" class="card-footer lw-whatsapp-bottom-sheet">
                <div class="list-group list-group-flush lw-whatsapp-buttons">
                    @foreach ($mediaValues['list_data']['sections'] as $section)
                    <div class="text-left">
                        <h3 class="mb-1">{{ $section['title'] }}</h3>
                        <dl class="text-dark">
                            @foreach ($section['rows'] as $sectionRow)
                            <dt><strong>{{ $sectionRow['title'] }}</strong> <small class="text-muted">({{  __tr('ID: ') }}{{ $sectionRow['row_id'] }})</small></dt>
                            <dd class="mb-4">{{ $sectionRow['description'] ?? '' }}</dd>
                            @endforeach
                        </dl>
                        <hr>
                    </div>
                    @endforeach
                    {{-- <button class="btn btn-sm btn-success">{{  __tr('Send') }}</button> --}}
                </div>
            </div>
            </div>
            @endisset
        </div>
    </div>
</div>