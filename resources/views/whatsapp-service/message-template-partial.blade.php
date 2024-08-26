{{-- @dd($templateComponentValues) --}}
<div class="lw-whatsapp-preview-message-container">
    {{-- <img class="lw-whatsapp-preview-bg" src="{{ asset('imgs/wa-message-bg.png') }}" alt=""> --}}
    <div class="lw-whatsapp-preview">
        <div class="card ">
            {{-- @dd($templateComponents) --}}
            @foreach ($templateComponents as $templateComponent)
            @if ($templateComponent['type'] == 'HEADER')
            @if ($templateComponent['format'] != 'TEXT')
            <div class="lw-whatsapp-header-placeholder">
                @if ($templateComponent['format'] == 'LOCATION')
                <iframe height="100" src="https://maps.google.com/maps/place?q={{ $headerItemValues['location']['latitude'] ?? '' }},{{ $headerItemValues['location']['longitude'] ?? '' }}&output=embed&language={{ app()->getLocale() }}" frameborder="0" scrolling="no"></iframe>
                @elseif ($templateComponent['format'] == 'VIDEO')
                <video class="lw-whatsapp-header-video" controls src="{{ $headerItemValues['video'] }}"></video>
                @elseif ($templateComponent['format'] == 'IMAGE')
                <a class="lw-wa-message-document-link" target="_blank" href="{{ $headerItemValues['image'] }}"><img class="lw-whatsapp-header-image" src="{{ $headerItemValues['image'] }}" alt=""></a>
                @elseif ($templateComponent['format'] == 'DOCUMENT')
                <a class="lw-wa-message-document-link" title="{{ __tr('Document Link') }}" target="_blank" href="{{ $headerItemValues['document'] }}"><i class="fa fa-5x fa-file-alt text-white"></i></a>
                @endif
            </div>
            @endif
            @if ($templateComponent['format'] == 'LOCATION')
            <div class="lw-whatsapp-location-meta bg-secondary p-2">
                <small>{{ $headerItemValues['location']['name'] ?? '' }}</small><br>
                <small>{{ $headerItemValues['location']['address'] ?? '' }}</small>
            </div>
            @elseif ($templateComponent['format'] == 'TEXT')
            <div class="lw-whatsapp-body mb--3">
                @php
                $exampleHeaderItems = [
                "\n" => '<br>',
                ];
                @endphp
                @isset($templateComponent['example'])
                @php
                $headerTextItems = $templateComponent['example']['header_text'];
                $exampleHeaderTextItemIndex = 1;
                foreach ($headerTextItems as $headerTextItem) {
                    $exampleHeaderItems["{{{$exampleHeaderTextItemIndex}}}"] = $headerItemValues['text'][$exampleHeaderTextItemIndex-1] ?? '';
                    $exampleHeaderTextItemIndex++;
                }
                @endphp
                @endisset
                <strong><?= strtr($templateComponent['text'], $exampleHeaderItems) ?></strong>
            </div>
            @endif
            @endif
            @if ($templateComponent['type'] == 'BODY')
            <div class="lw-whatsapp-body">
                @php
                $exampleBodyItems = [
                "\n" => '<br>',
                ];
                @endphp
                <?= formatWhatsAppText(strtr($templateComponent['text'], array_merge($exampleBodyItems, $bodyItemValues))) ?>
            </div>
            @endif
            @if ($templateComponent['type'] == 'FOOTER')
            <div class="lw-whatsapp-footer text-muted">
                {{ $templateComponent['text'] }}
            </div>
            @endif
            @if($templateComponent['type'] == 'BUTTONS')
            <div class="card-footer lw-whatsapp-buttons">
                <div class="list-group list-group-flush lw-whatsapp-buttons" x-data="{seeAllOptions:false}">
                    @foreach ($templateComponent['buttons'] as $templateComponentButton)
                    <div class="list-group-item" @if($loop->count > 2) x-show="seeAllOptions" @endif>
                        @if ($templateComponentButton['type'] == 'URL')
                        <a target="_blank" href="{{ strtr($templateComponentButton['url'], $buttonValues) }}"><i class="fas fa-external-link-square-alt"></i> {{ $templateComponentButton['text'] }}</a>
                        @elseif ($templateComponentButton['type'] == 'QUICK_REPLY')
                        <i class="fa fa-reply"></i>
                        @elseif ($templateComponentButton['type'] == 'PHONE_NUMBER')
                        <i class="fa fa-phone-alt"></i>
                        @elseif ($templateComponentButton['type'] == 'COPY_CODE')
                        <i class="fa fa-copy"></i>
                        @endif
                        @if ($templateComponentButton['type'] != 'URL')
                        {{ $templateComponentButton['text'] }}
                        @endif
                    </div>
                    @if(($loop->count > 2) and ($loop->index == 1))
                    <div class="list-group-item"><button @click="seeAllOptions = !seeAllOptions" class="btn btn-sm btn-light btn-block"><i class="fa fa-menu"></i> <span x-show="!seeAllOptions">{{ __tr('See all options') }}</span><span x-show="seeAllOptions">{{ __tr('Hide all options') }}</span></button></div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>