@php
/**
* File          : manage-master.blade.php
----------------------------------------------------------------------------- */
@endphp
@extends('layouts.app', ['title' => ($pageTitle ?? '')])
@section('content')
    @include('users.partials.header', [
    'title' => __tr('Translations') . ' '. auth()->user()->name,
    'description' => __tr('__languageName__ Language Translations (__translationsCount__)', [
    '__languageName__' => $languageInfo['name'],
    '__translationsCount__' => $translations->count()
    ]),
    'class' => 'col-lg-12'
    ])
    <!-- Start of Page Wrapper -->
    <div class="container-fluid">
    
    <div class="row">
        <div class="col-xl-12">
            <div class="alert alert-warning">
                <strong><?= __tr('Please note') ?></strong> <?= __tr('Google Auto Translate given here is API key less method your IP may get BLOCKED for particular time, if too much requests are done.') ?>
            </div>
        </div>
        <div class="col-xl-12 text-right mb-2">
            <div class="btn-group" role="group">
                <a class="btn btn-light" href="<?= route('manage.translations.languages') ?>" title="<?= __tr('Back to languages') ?>"><i class="fa fa-arrow-left"></i> <?= __tr('Back to languages') ?></a>
                 <a class="btn btn-secondary lw-ajax-link-action" href="<?= route('manage.translations.scan', [
                     'languageId' => $languageInfo['id'],
                 ]) ?>" title="<?= __tr('Re-Scan') ?>"><i class="fa fa-sync-alt"></i> <?= __tr('Re-Scan') ?></a>
                 <!-- translation services dropdown -->
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= __tr('Auto Translations') ?>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                    <!-- Google Spreadsheet tool -->
                    <!-- Other services -->
                    @if (getAppSettings("microsoft_translator_api_key")) <a class="dropdown-item lw-ajax-link-action" data-method="post" href="<?= route('manage.translations.auto_translate', [
    'serviceId' => 'microsoft',
    'languageId' => $languageInfo['id'],
]) ?>" title="<?= __tr('Re-Scan') ?>"><i class="fa fa-sync-alt"></i>{{ __tr('Microsoft Translator') }}</a>
                        @else
                            <a href="#" disabled class="disabled dropdown-item">{{ __tr('Microsoft Translator - Key not added') }}</a>
                       @endif
                    <a class="dropdown-item" href="#autoTranslationDialog" data-toggle="modal" data-target="#autoTranslationDialog">
                        <?= __tr('Auto translations (Google Spreadsheet)') ?></span>
                    </a>
                </div>
            </div>


             </div>
        </div>
    </div>

     <!-- Modal -->
     <div class="modal fade" id="autoTranslationDialog" tabindex="-1" role="dialog" aria-labelledby="autoTranslationLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="autoTranslationLabel"><?= __tr('Auto translations using Google Spreadsheet') ?></h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <!-- Step 1 -->
                     <div>
                         <h5><?= __tr('Step 1') ?></h5>
                         <!-- Export button -->
                         <a class="btn btn-secondary btn-sm mt-2" target="_blank" href="<?= route('manage.translations.export', ['languageId' => $languageInfo['id']]) ?>" title="<?= __tr('Export') ?>"><i class="fas fa-file-export"></i> <?= __tr('Export Translation Strings to XLSX file') ?></a>
                         <!-- /Export button -->
                     </div>
                     <!-- /Step 1 -->
                     <hr class="border-top">
                     <!-- Step 2 -->
                     <div class="mt-2">
                         <h5><?= __tr('Step 2') ?></h5>
                         <p>
                             <?= __tr('Once downloaded file, Go to __googleSpreadSheetAnchorTag__ and Import the exported spreadsheet using upload.', [
                                 '__googleSpreadSheetAnchorTag__' =>
                                     '<a target="_blank" href="https://docs.google.com/spreadsheets/create">
                            ' .
                                     __tr('Google Spreadsheets') .
                                     '
                            </a>',
                             ]) ?>
                         </p>
                     </div>
                     <!-- /Step 2 -->
                     <hr class="border-top">
                     <!-- Step 3 -->
                     <div class="mt-2">
                         <h5><?= __tr('Step 3') ?> <small>{{  __tr('(This Step may not required.)') }}</small></h5>
                         <?= __tr('Then go to Edit Menu and Select Find and Replace option, now find __doubleQuotes__ and replace with __singleQuote__, Also check the option called "Also check within formulas" and click on Replace All button', [
                             '__doubleQuotes__' => '<code>==</code>',
                             '__singleQuote__' => '<code>=</code>',
                         ]) ?>
                     </div>
                     <!-- /Step 3 -->
                     <hr class="border-top">
                     <!-- Step 4 -->
                     <div class="mt-2">
                         <h5><?= __tr('Step 4') ?></h5>
                         <?= __tr('Now wait until, it translate all your string automatically, Now just export using __downloadFunctionPath__ and Drag & Drop or Browse your excel file to process the translations below.', [
                             '__downloadFunctionPath__' => '<code> File > Download > Microsoft Excel (.xlsx) </code>',
                         ]) ?>
                     </div>
                     <!-- /Step 4 -->

                     <div class="col-lg-9 mt-2">
                         <input type="file" name="filepond" class="filepond lw-file-uploader mt-5" id="lwFileUploader" data-remove-media="true" data-instant-upload="true" data-allowed-media='<?= getMediaRestriction('language') ?>' data-action="<?= route('manage.translations.import', ['languageId' => $languageInfo['id']]) ?>" data-label-idle="<span class='filepond--label-action'><?= __tr('Import & Process') ?></span>" data-callback="afterSuccessfullyUploaded">
                     </div>
                 </div>

                 <div class="modal-footer">
                     <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"><?= __tr('Cancel') ?></button>
                 </div>
             </div>
         </div>
     </div>

     <!-- Start of Page Wrapper -->
     <div class="row">
         <div class="col-xl-12 mb-4 <?= $languageInfo['is_rtl'] ? 'lw-lang-direction-rtl' : 'lw-lang-direction-ltr' ?>"><?php $lineCount = 1; ?>
             @foreach ($translations as $translationsItemKey => $translationsItem)
             <div class="card mb-4">
                 <div class="card-header lw-original-text-line">
                     <?= $translationsItem->getOriginal() ?>
                 </div>
                 <div class="card-body">
                     <form class="row lw-ajax-form lw-form" method="post" action="<?= route('manage.translations.update') ?>" data-show-processing="true">
                         <div class="input-group mb-3">
                             <?php if ($translationsItem->getPlural()): ?>
                                 <div class="input-group-prepend">
                                     <div class="input-group-text"><?= __tr('Singular') ?></div>
                                 </div>
                             <?php endif; ?>
                             <input type="text" class="form-control" name="message_str" id="<?= $translationsItemKey ?>" value="<?= $translationsItem->getTranslation() ?>">
                             <input type="hidden" name="message_id" value="<?= $translationsItem->getOriginal() ?>">
                             <input type="hidden" name="message_for_translate" value="<?= $translationsItem->getOriginal() ?>">
                             <input type="hidden" name="id" value="<?= $translationsItemKey ?>">
                             <input type="hidden" name="language_id" value="<?= $languageId ?>">
                             <input type="hidden" name="old_message_str" value="<?= $translationsItem->getTranslation() ?>">
                             <div class="input-group-append">
                                 <button class="btn btn-outline-light lw-auto-translate-action" type="button" title="<?= __tr('Google Auto Translate') ?>"><i class="fa fa-language"></i> <?= __tr('Auto Translate') ?></button>
                                 <button class="btn btn-light lw-save-translation lw-ajax-form-submit-action" type="button"><?= __tr('Save') ?></button>
                             </div>
                         </div>
                     </form>
                     <?php if ($translationsItem->getPlural()): ?>
                         <form class="row lw-ajax-form lw-form" method="post" action="<?= route('manage.translations.update') ?>" data-show-processing="true">
                             <label for="<?= $translationsItemKey ?>"><?= $translationsItem->getPlural() ?></label>
                             <div class="input-group mb-3">
                                 <div class="input-group-prepend">
                                     <div class="input-group-text"><?= __tr('Plural') ?></div>
                                 </div>
                                 <input type="text" class="form-control" name="message_str_plural" id="<?= $translationsItemKey ?>Plural" value="<?= $translationsItem->getPluralTranslations(2)[0] ?>">
                                 <input type="hidden" name="message_id" value="<?= $translationsItem->getOriginal() ?>">
                                 <input type="hidden" name="message_for_translate" value="<?= $translationsItem->getPlural() ?>">
                                 <input type="hidden" name="is_plural" value="true">
                                 <input type="hidden" name="id" value="<?= $translationsItemKey ?>Plural">
                                 <input type="hidden" name="language_id" value="<?= $languageId ?>">
                                 <input type="hidden" name="old_message_str_plural" value="<?= $translationsItem->getPluralTranslations(2)[0] ?>">
                                 <div class="input-group-append">
                                     <button class="btn btn-outline-light lw-auto-translate-action" type="button" title="<?= __tr('Google Auto Translate') ?>"><i class="fa fa-language"></i> <?= __tr('Auto Translate') ?></button>
                                     <button class="btn btn-light lw-save-translation lw-ajax-form-submit-action" type="button"><?= __tr('Save') ?></button>
                                 </div>
                             </div>
                         </form>
                     <?php endif; ?>
                     <?php $lineCount++; ?>

                 </div>
             </div>
             @endforeach
         </div>
     </div>
    </div>
@endsection

 @push('appScripts')
     <script>
    (function($) {
        'use strict';
         $('.lw-auto-translate-action').on('click', function(e) {
             var $this = $(this),
             $thisForm = $this.parents('form'),
             formData = __Utils.queryConvertToObject($thisForm.serialize());
             __DataRequest.post("https://translate.googleapis.com/translate_a/single", {
                 client: 'gtx',
                 sl: 'en',
                 tl: '<?= $languageId ?>',
                 dt: 't',
                 q: formData.message_for_translate
             }, function(responseData) {
                 var translatedStrings = responseData[0],
                     wholeString = '';
                 for (const translatedStringKey in translatedStrings) {
                     wholeString += translatedStrings[translatedStringKey][0];
                 };
                 if (formData.is_plural) {
                     $thisForm.find('[name=message_str_plural]').val(
                         wholeString
                     );
                 } else {
                     $thisForm.find('[name=message_str]').val(
                         wholeString
                     );
                 };
                 showSuccessMessage('<?= __tr('Auto Translation fetched Successfully') ?>');

             }, {
                 csrf: false
             }).then(function(e) {
                 if (e.status !== 200) {
                     alert("<?= __tr('Google Auto Translate given here is API key less method your IP may get BLOCKED for particular time, if too much requests are done. Check again after few hours or change your internet connection to change IP address') ?>");

                     showSuccessMessage('<?= __tr('Failed to get auto translation') ?>');
                 }
             });
         });

         window.afterSuccessfullyUploaded = function (responseData) {
             if (responseData.reaction == 1) {
                 __Utils.viewReload();
             };
         };
    })(jQuery);
     </script>
 @endpush
