<?php $translationLanguages = getActiveTranslationLanguages();
$configCurrentLocale = app()->getLocale();
?>
<!-- Language Menu -->
@if (!__isEmpty($translationLanguages) and (count($translationLanguages) > 1))
<li class="nav-item dropdown no-arrow">
    <a class="nav-link dropdown-toggle" href="#" id="translationMenuDropdown" role="button" data-bs-toggle="dropdown"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="d-md-inline-block">
            {{ isset($translationLanguages[$configCurrentLocale]) ? $translationLanguages[$configCurrentLocale]['name'] : '' }}
        </span>
        &nbsp; <i class="fas fa-language"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-end shadow animated--grow-in" aria-labelledby="translationMenuDropdown">
        <li class="dropdown-item dropdown-header text-gray disabled">
            {{ __tr('Choose your language') }}
        </li>
        <li class="dropdown-divider"></li>
        <?php foreach ($translationLanguages as $languageId => $language) {
            if (($languageId == $configCurrentLocale) or (isset($language['status']) and $language['status'] == false)) continue;
        ?>
        <li><a class="dropdown-item lw-ajax-link-action" data-show-processing="true" href="{{ route('locale.change', ['localeID' => $languageId]) }}">{{ $language['name'] }}</a></li>
        <?php } ?>
    </ul>
</li>
@endif
<!-- Language Menu -->