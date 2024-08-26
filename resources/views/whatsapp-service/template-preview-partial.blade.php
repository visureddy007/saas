<div class="lw-whatsapp-preview-container">
    <img class="lw-whatsapp-preview-bg" src="{{ asset('imgs/wa-message-bg.png') }}" alt="">
    <div class="lw-whatsapp-preview">
        <div class="card ">
            @foreach ($templateComponents as $templateComponent)
            @if ($templateComponent['type'] == 'HEADER')
            @if ($templateComponent['format'] != 'TEXT')
            <div class="lw-whatsapp-header-placeholder">
                @if ($templateComponent['format'] == 'LOCATION')
                <i class="fa fa-5x fa-map-marker-alt text-white"></i>
                @elseif ($templateComponent['format'] == 'VIDEO')
                <i class="fa fa-5x fa-play-circle text-white"></i>
                @elseif ($templateComponent['format'] == 'IMAGE')
                <i class="fa fa-5x fa-image text-white"></i>
                @elseif ($templateComponent['format'] == 'DOCUMENT')
                <i class="fa fa-5x fa-file-alt text-white"></i>
                @endif
            </div>
            @endif
            @if ($templateComponent['format'] == 'LOCATION')
            <div class="lw-whatsapp-location-meta bg-secondary p-2">
                <small>@{{location_name}}</small><br>
                <small>@{{address}}</small>
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
                    $exampleHeaderItems["{{{$exampleHeaderTextItemIndex}}}"] = "{{Header $exampleHeaderTextItemIndex}}";
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
                <?= formatWhatsAppText(strtr($templateComponent['text'], $exampleBodyItems)) ?>
            </div>
            @endif
            @if ($templateComponent['type'] == 'FOOTER')
            <div class="lw-whatsapp-footer text-muted">
                {{ $templateComponent['text'] }}
            </div>
            @endif
            @if($templateComponent['type'] == 'BUTTONS')
            <div class="card-footer lw-whatsapp-buttons">
                <div class="list-group list-group-flush lw-whatsapp-buttons">
                    @foreach ($templateComponent['buttons'] as $templateComponentButton)
                    <div class="list-group-item">
                        @if ($templateComponentButton['type'] == 'URL')
                        <i class="fas fa-external-link-square-alt"></i>
                        @elseif ($templateComponentButton['type'] == 'QUICK_REPLY')
                        <i class="fa fa-reply"></i>
                        @elseif ($templateComponentButton['type'] == 'PHONE_NUMBER')
                        <i class="fa fa-phone-alt"></i>
                        @elseif ($templateComponentButton['type'] == 'VOICE_CALL')
                        <i class="fa fa-phone-alt"></i>
                        @elseif ($templateComponentButton['type'] == 'COPY_CODE')
                        <i class="fa fa-copy"></i>
                        @endif
                        {{ $templateComponentButton['text'] }}
                    </div>
                    @if(($loop->count > 2) and ($loop->index == 1))
                    <div class="list-group-item"><i class="fa fa-menu"></i> {{ __tr('See all options') }} <br><small class="text-orange">{{  __tr('More than 3 buttons will be shown in the list by clicking') }}</small></div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>