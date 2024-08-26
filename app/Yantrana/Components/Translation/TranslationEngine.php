<?php

/**
 * TranslationEngine.php - Main component file
 *
 * This file is part of the Translation component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Translation;

use Exception;
use XLSXWriter;
use Carbon\Carbon;
use Gettext\Merge;
use Gettext\Translations;
use Illuminate\Support\Arr;
use Gettext\Loader\PoLoader;
use Gettext\Scanner\PhpScanner;
use App\Yantrana\Base\BaseEngine;
use Gettext\Generator\MoGenerator;
use Gettext\Generator\PoGenerator;
use Illuminate\Support\Facades\Http;
use App\Yantrana\Components\Media\MediaEngine;
use Unn\GettextBlade\Scanner\Blade as BladeScanner;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use App\Yantrana\Components\Translation\Interfaces\TranslationEngineInterface;
use App\Yantrana\Components\Configuration\Repositories\ConfigurationRepository;

class TranslationEngine extends BaseEngine implements TranslationEngineInterface
{
    /**
     * @var  ConfigurationRepository $configurationRepository - Configuration Repository
     */
    protected $configurationRepository;

    /**
     * @var  MediaEngine $mediaEngine - Media Engine
     */
    protected $mediaEngine;

    /**
     * Constructor
     *
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    public function __construct(
        ConfigurationRepository $configurationRepository,
        MediaEngine $mediaEngine
    ) {
        $this->configurationRepository  = $configurationRepository;
        $this->mediaEngine              = $mediaEngine;
    }

    public function languages()
    {
        return $this->engineResponse(1, [
            'languages' => getAppSettings('translation_languages')
        ]);
    }

    /**
     * Process Store Language
     *
     * @param array $inputData
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    public function processStoreLanguage($inputData)
    {
        $translationLanguage = $this->configurationRepository->fetchByName('translation_languages');
        $now = Carbon::now()->toDateTimeString();
        // prepare language data
        $languageData = [
            $inputData['language_id'] => [
                'id' => $inputData['language_id'],
                'name' => $inputData['language_name'],
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
                'is_rtl' => ($inputData['is_rtl'] == 'true') ? true : false
            ]
        ];
        // check if existing translation language exists
        if (\__isEmpty($translationLanguage)) {
            $languageTranslationStoreData = [
                'name' => 'translation_languages',
                'value' => json_encode($languageData),
                'data_type' => 4
            ];
        } else {
            $existingTranslationLanguages = array_merge(json_decode($translationLanguage->value, true), $languageData);
            // Check if existing entry deleted successfully
            if (!$this->configurationRepository->deleteConfiguration(['translation_languages'])) {
                return $this->engineResponse(2, null, __tr('Something went wrong on server.'));
            }
            // Prepare data for store translation languages
            $languageTranslationStoreData = [
                'name' => 'translation_languages',
                'value' => json_encode($existingTranslationLanguages),
                'data_type' => 4
            ];
        }
        // check if translation language stored
        if ($this->configurationRepository->storeTranslationLanguage($languageTranslationStoreData)) {
            $this->scan($inputData['language_id']);
            if(isset($inputData['auto_translate']) and $inputData['auto_translate'] and ($inputData['auto_translate'] == 'microsoft')) {
                $this->processTranslatePoFile('microsoft', $inputData['language_id']);
            }
            return $this->engineResponse(1, null, __tr('Translation language stored successfully.'));
        }

        return $this->engineResponse(2, null, __tr('Translation language not stored.'));
    }

    /**
     * Process Update Language
     *
     * @param array $inputData
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    public function processUpdateLanguage($inputData)
    {
        $translationLanguage = $this->configurationRepository->fetchByName('translation_languages');

        // check if translation language exists
        if (\__isEmpty($translationLanguage)) {
            return $this->engineResponse(18, null, __tr('Translation language does not exists.'));
        }

        // convert json object to array
        $existingTranslationLanguages = json_decode($translationLanguage->value, true);
        $languageKey = $inputData['form_key'];

        $existingTranslationLanguages[$languageKey]['name'] = $inputData['language_name_' . $languageKey];
        $existingTranslationLanguages[$languageKey]['updated_at'] = Carbon::now()->toDateTimeString();
        $existingTranslationLanguages[$languageKey]['is_rtl'] = ($inputData['is_rtl_' . $languageKey] == 'true') ? true : false;
        $existingTranslationLanguages[$languageKey]['status'] = ($inputData['status_' . $languageKey] == 'true') ? true : false;

        // Check if existing entry deleted successfully
        if (!$this->configurationRepository->deleteConfiguration(['translation_languages'])) {
            return $this->engineResponse(2, null, __tr('Something went wrong on server.'));
        }

        // Prepare data for store translation languages
        $languageTranslationStoreData = [
            'name' => 'translation_languages',
            'value' => json_encode($existingTranslationLanguages),
            'data_type' => 4
        ];

        // check if translation language stored
        if ($this->configurationRepository->storeTranslationLanguage($languageTranslationStoreData)) {
            return $this->engineResponse(1, null, __tr('Translation language stored successfully.'));
        }

        return $this->engineResponse(2, null, __tr('Translation language not stored.'));
    }

    /**
     * Process Delete Language
     *
     * @param array $inputData
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    public function processDeleteLanguage($languageId)
    {
        $translationLanguage = $this->configurationRepository->fetchByName('translation_languages');

        // check if translation language exists
        if (\__isEmpty($translationLanguage)) {
            return $this->engineResponse(18, null, __tr('Translation language does not exists.'));
        }

        // convert json object to array
        $existingTranslationLanguages = json_decode($translationLanguage->value, true);
        // unset existing language
        unset($existingTranslationLanguages[$languageId]);
        // Check if existing entry deleted successfully
        if (!$this->configurationRepository->deleteConfiguration(['translation_languages'])) {
            return $this->engineResponse(2, null, __tr('Something went wrong on server.'));
        }

        // Prepare data for store translation languages
        $languageTranslationStoreData = [
            'name' => 'translation_languages',
            'value' => json_encode($existingTranslationLanguages),
            'data_type' => 4
        ];

        // check if translation language stored
        if ($this->configurationRepository->storeTranslationLanguage($languageTranslationStoreData)) {
            if ($languageId) {
                $this->deleteDir(\base_path('locale/' . $languageId));
            }

            return $this->engineResponse(1, null, __tr('Translation language deleted successfully.'));
        }

        return $this->engineResponse(2, null, __tr('Translation language not stored.'));
    }

    public function lists($languageId = 'en')
    {
        $languageInfo = $this->verifiedOrAbort($languageId);

        $existingPoFile = $this->getFilePath($languageId, 'po');

        if (!\file_exists($existingPoFile)) {
            $this->scan($languageId);
        }

        //import from a .po file:
        $loader = new PoLoader();
        $translations = $loader->loadFile($existingPoFile);

        return $this->engineResponse(1, [
            'translations' => $translations,
            'languageInfo' => $languageInfo
        ]);
    }

    protected function verifiedOrAbort($languageId)
    {
        $translationLanguage = $this->configurationRepository->fetchByName('translation_languages');

        // check if translation language exists
        if (\__isEmpty($translationLanguage)) {
            abort(404);
        }

        // convert json object to array
        $existingTranslationLanguages = json_decode($translationLanguage->value, true);

        $languageInfo = array_get($existingTranslationLanguages, $languageId);

        if (!$languageInfo) {
            abort(404);
        }

        return $languageInfo;
    }

    protected function rGlob($folders, $pattern): array
    {
        if (!is_array($folders)) {
            $folders = [$folders];
        }
        $fileList = array();
        foreach ($folders as $folder) {
            $dir = new \RecursiveDirectoryIterator($folder);
            $ite = new \RecursiveIteratorIterator($dir);
            $files = new \RegexIterator($ite, $pattern, \RegexIterator::GET_MATCH);
            foreach ($files as $file) {
                if (\file_exists($file[0])) {
                    $fileList = array_merge($fileList, $file);
                }
            }
        }
        return $fileList;
    }

    public function scan($languageId)
    {
        $translationLanguage = $this->configurationRepository->fetchByName('translation_languages');

        // check if translation language exists
        if (\__isEmpty($translationLanguage)) {
            return $this->engineResponse(18, null, __tr('Translation languages does not exists.'));
        }

        // convert json object to array
        $existingTranslationLanguages = json_decode($translationLanguage->value, true);

        $languageInfo = array_get($existingTranslationLanguages, $languageId);

        if (!$languageInfo) {
            return $this->engineResponse(18, null, __tr('Translation language does not exists.'));
        }

        // grab the list of the files to scan
        $filesToScan = $this->rGlob([
            // whole app folder
            base_path('app'),
            // resources folder views etc
            base_path('resources'),
            // config folder
            base_path('config'),
        ], '/.*php/');

        $translations = Translations::create('messages');
        //Create a new scanner, adding a translation for each domain we want to get:
        $phpScanner = new BladeScanner($translations);
        //Set a default domain, so any translations with no domain specified, will be added to that domain
        $phpScanner->setDefaultDomain('messages');
        // ignore invalid gettext function
        $phpScanner->ignoreInvalidFunctions();
        // our gettext helper function
        $phpScanner->setFunctions([
            '__trn' => 'ngettext',
            '__tr' => 'gettext',
            '__' => 'gettext'
        ]);
        //Extract all comments starting with 'i18n:' and 'Translators:'

        //Scan files
        foreach ($filesToScan as $file) {
            $phpScanner->scanFile($file);
        }
        // load po file
        $existingPoFile = $this->getFilePath($languageId, 'po');
        //S new po loader
        $loader = new PoLoader();
        $isUpdate = false;

        if (\file_exists($existingPoFile)) {
            $strategy = Merge::SCAN_AND_LOAD; //Merge::TRANSLATIONS_OURS | Merge::HEADERS_OURS;

            $previousEntries = $loader->loadFile($existingPoFile);
            $translations = $translations->mergeWith($previousEntries, $strategy);
            $isUpdate = true;
        } else {
            if (!\is_dir($this->getFilePath($languageId))) {
                \mkdir($this->getFilePath($languageId), 0777, true);
            }
        }
        // get file headers
        $headers = $translations->getHeaders();
        // set the headers
        $headers->set('Project-Id-Version', config('app.name'));
        $headers->set('PO-Revision-Date', now());
        $headers->set('Last-Translator', getUserAuthInfo('profile.full_name'));
        $headers->set('Language', $languageId);
        // Meta for PoEdit
        $headers->set('X-Poedit-KeywordsList', "__;__tr;__trn\n");

        $poGenerator = new PoGenerator();
        if ($poGenerator->generateFile($translations, $existingPoFile)) {
            //export to a .mo file:
            $moGenerator = new MoGenerator();
            $moGenerator->generateFile($translations, $this->getFilePath($languageId, 'mo'));
            // if its updated
            if ($isUpdate) {
                return $this->engineResponse(1, null, __tr("Source files scanned & PO/MO files updated successfully"));
            }
            // if is newly created
            return $this->engineResponse(1, null, __tr("Source files scanned & PO/MO files generated successfully"));
        }

        if ($isUpdate) {
            return $this->engineResponse(1, null, __tr("PO/MO files updated successfully"));
        }

        return $this->engineResponse(1, null, __tr("PO/MO files generated successfully"));
    }

    public function update($inputData)
    {
        $languageId = array_get($inputData, 'language_id');

        $existingPoFile = $this->getFilePath($languageId, 'po');

        if (!\file_exists($existingPoFile)) {
            $this->scan($languageId);
        }

        //import from a .po file:
        $loader = new PoLoader();
        $translations = $loader->loadFile($existingPoFile);

        $messageId = array_get($inputData, 'message_id');
        $messageStr = array_get($inputData, 'message_str');
        $isPlural = array_get($inputData, 'is_plural', false);
        $messageStrPlural = array_get($inputData, 'message_str_plural');

        if ($isPlural) {
            $messageStr = $messageStrPlural;
        }

        if (!$messageStr) {
            $messageStr = '';
        }
        // find required translation
        $translation = $translations->find(null, $messageId);

        // set translation text
        if ($translation) {
            if ($isPlural) {
                $translation->translatePlural($messageStr);
            } else {
                $translation->translate($messageStr);
            }
        }

        //Save the translations in .po files
        $generator = new PoGenerator();
        if ($generator->generateFile($translations, $existingPoFile)) {
            //export to a .mo file:
            $generator = new MoGenerator();
            $generator->generateFile($translations, $this->getFilePath($languageId, 'mo'));
        }
        // as the message not present its seems that user wants to clear it
        if (!$messageStr) {
            return $this->engineResponse(1, null, __tr('Translation has been cleared'));
        }
        // everything looks good
        return $this->engineResponse(1, null, __tr('Translation updated successfully'));
    }

    protected function getFilePath($languageId, $fileType = null)
    {
        if ($fileType) {
            return \base_path("locale/{$languageId}/LC_MESSAGES/messages.{$fileType}");
        }
        return \base_path("locale/{$languageId}/LC_MESSAGES");
    }

    protected function deleteDir($dirPath)
    {
        if (!file_exists($dirPath)) {
            return;
        }
        $it = new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator(
            $it,
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dirPath);
    }

    /*
    * Export to excel
    *
    */
    public function exportToExcel($languageId = 'en')
    {
        $languageInfo = $this->verifiedOrAbort($languageId);

        $existingPoFile = $this->getFilePath($languageId, 'po');

        if (!\file_exists($existingPoFile)) {
            $this->scan($languageId);
        }

        //import from a .po file:
        $loader = new PoLoader();
        $translations = $loader->loadFile($existingPoFile);

        $exportTranslations = [];
        $sheetColumn = 1;

        $languageId = substr($languageId, 0, 2);

        if (!__isEmpty($translations)) {
            foreach ($translations as $translationsItemKey => $translationsItem) {
                $translation = $translations->find(null, $translationsItem->getOriginal());

                //formula for translations
                $formula = strtr('=GOOGLETRANSLATE(A__record_key__, "__def_language__", "__output_lang__")', [
                    "__def_language__" => "en",
                    "__output_lang__" => $languageId,
                    "__record_key__" => $sheetColumn
                ]);

                $exportTranslations[] =  array(
                    $translationsItem->getOriginal(), $formula
                );

                if ($translation->getPlural()) {
                    $sheetColumn++;
                    $formula = strtr('=GOOGLETRANSLATE(A__record_key__, "__def_language__", "__output_lang__")', [
                        "__def_language__" => "en",
                        "__output_lang__" => $languageId,
                        "__record_key__" => $sheetColumn
                    ]);
                    $exportTranslations[] =  array(
                        $translation->getPlural(), $formula
                    );
                }

                $sheetColumn++;
            }
        }

        //create temp path for store excel file
        $temp_file = tempnam(sys_get_temp_dir(), 'translations.xlsx');

        $writer = new XLSXWriter();

        $writer->writeSheet($exportTranslations, 'Sheet1'); //or write the whole sheet in 1 call

        $writer->writeToFile($temp_file);

        $headers = [
            'Content-Transfer-Encoding: binary',
            'Content-Type: application/octet-stream',
        ];


        return response()->download($temp_file, 'translations.xlsx', $headers)->deleteFileAfterSend();
    }

    /*
    * Import Excel
    *
    */
    public function importExcel($inputData, $languageId = 'en')
    {
        try {
            $uploadedFile = $this->mediaEngine->processUploadTranslationFile($inputData, 'language');

            if ($uploadedFile['reaction_code'] == 1) {
                $filePath = \storage_path('app/' . getPathByKey('language_file') . '/' . $uploadedFile['data']['fileName']);
                $reader = ReaderEntityFactory::createReaderFromFile($filePath);

                $existingPoFile = $this->getFilePath($languageId, 'po');
                if (!\file_exists($existingPoFile)) {
                    $this->scan($languageId);
                }
                //import from a .po file:
                $loader = new PoLoader();
                $translations = $loader->loadFile($existingPoFile);
                $pluralMessageKeys = [];

                $reader->open($filePath);
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                        $rowArray = $row->toArray();

                        $messageId = array_get($rowArray, '0');
                        $messageStr = array_get($rowArray, '1');

                        if (__isEmpty($messageId)) {
                            continue;
                        }

                        $translation = $translations->find(null, $messageId);

                        // set translation text
                        if ($translation) {
                            if ($translation->getPlural()) {
                                $pluralMessageKeys[$rowIndex + 1] = $messageId;
                            }

                            if (!$translation->isTranslated()) {
                                $translation->translate($messageStr);
                            }
                        }
                    }
                }

                // Check if plural messages keys are exists
                if (!__isEmpty($pluralMessageKeys)) {
                    foreach ($reader->getSheetIterator() as $sheetForPlural) {
                        foreach ($sheetForPlural->getRowIterator() as $pluralRowIndex => $pluralMsgRow) {
                            if (array_key_exists($pluralRowIndex, $pluralMessageKeys)) {
                                $pluralMsgRowArray = $pluralMsgRow->toArray();
                                $pluralMessageId = $pluralMessageKeys[$pluralRowIndex];
                                $pluralMessageStr = array_get($pluralMsgRowArray, '1');

                                $translation = $translations->find(null, $pluralMessageId);

                                if ($translation) {
                                    if (__isEmpty($translation->getPluralTranslations(2)[0])) {
                                        $translation->translatePlural($pluralMessageStr);
                                    }
                                }
                            }
                        }
                    }
                }
                //Save the translations in .po files
                $generator = new PoGenerator();
                $generator->generateFile($translations, $existingPoFile);

                //export to a .mo file:
                $generator = new MoGenerator();
                $generator->generateFile($translations, $this->getFilePath($languageId, 'mo'));

                @unlink($filePath);
            }

            return $this->engineResponse(1, null, __tr('Messages translated successfully.'));
        } catch (Exception $e) {
            return $this->engineResponse(2, null, __tr('Something went wrong while processing your request'));
        }
    }

    /**
     * Translating PO files  using available services
     *
     * @param string $serviceId
     * @param string $languageId
     * @param string $domain
     * @return void
     */
    public function processTranslatePoFiles(string $serviceId, string $domain = 'messages')
    {
        updateProgressTextModel(
            __tr('preparing languages ...')
        );
        $languages = $this->languages()->data('languages');

        if(__isEmpty($languages)) {
            return $this->engineResponse(14, null, __tr('No languages to translate'));
        }
        try {
            foreach ($languages as $languageKey => $languageValue) {
                updateProgressTextModel(
                    __tr('translating __languageName__ language  ...', [
                        '__languageName__' => strtolower($languageValue['name'])
                    ])
                );
                $this->processTranslatePoFile($serviceId, $languageKey);
            }
        } catch (\Exception $e) {
            updateProgressTextModel('');
            return $this->engineResponse(2, null, $e->getMessage());
        }
        updateProgressTextModel('');
        return $this->engineResponse(1, null, __tr('All language messages translated successfully.'));
    }

    /**
     * Translating PO files  using available services
     *
     * @param string $serviceId
     * @param string $languageId
     * @param string $domain
     * @return void
     */
    public function processTranslatePoFile(string $serviceId, $languageId = 'en', string $domain = 'messages')
    {
        // get the po file path
        $existingPoFile = $this->getFilePath($languageId, 'po', $domain);
        // if not create by scanning
        // if (!\file_exists($existingPoFile)) {
        $this->scan($languageId);
        // }
        //import from a .po file:
        $loader = new PoLoader();
        // load the file
        $translations = $loader->loadFile($existingPoFile);
        // translations container
        $exportTranslations = [];
        // if not empty translation container
        if (!empty($translations)) {
            // go through each
            foreach ($translations as $translationsItemKey => $translationsItem) {
                // find translation string
                $translation = $translations->find(null, $translationsItem->getOriginal());
                // set if not translated
                if (!$translation->isTranslated()) {
                    $exportTranslations[]['text'] = $translationsItem->getOriginal();
                }
                // if plural
                if ($translation->getPlural()) {
                    // set if not translated
                    if (empty(Arr::get($translation->getPluralTranslations(2), '0', null))) {
                        $exportTranslations[]['text'] =  $translation->getPlural();
                    }
                }
            }
            // if translations container is empty
            if (empty($exportTranslations)) {
                return $this->engineResponse(14, null, __tr('Nothing to process'));
            }

            $translationServiceResult = [];
            $errorMessageIfAny = '';
            try {
                // check if service available
                if (
                    !config('services.translation.' . $serviceId)
                    or (config('services.translation.' . $serviceId . '.enabled') !== true)
                ) {
                    return $this->engineResponse(2, null, __tr('Service not Available'));
                }
                // request the service
                switch ($serviceId) {
                    // if the service id is microsoft
                    case 'microsoft':
                        $microsoftServiceResponse = $this->microsoftTranslate($languageId, $exportTranslations);
                        $translationServiceResult = $microsoftServiceResponse['translations'] ?? [];
                        $errorMessageIfAny = $microsoftServiceResponse['error_message'] ?? '';
                        break;

                    default:
                        return $this->engineResponse(2, null, __tr('Service not Available'));
                        break;
                }
            } catch (Exception $e) {
                // if in case of errors
                return $this->engineResponse(2, null, $e->getMessage());
            }
            // translation count index
            $countIndex = 0;

            // go through each translation
            foreach ($translations as $translationsItemKey => $translationsItem) {
                $isBasicTranslated = false;
                // find the string
                $translation = $translations->find(null, $translationsItem->getOriginal());
                // set if not translated
                if (!$translation->isTranslated()) {
                    $isBasicTranslated = true;
                    $translation->translate(Arr::get($translationServiceResult, (string) $countIndex, ''));
                }
                // if plural
                if ($translation->getPlural()) {
                    // set if not translated
                    if (empty(Arr::get($translation->getPluralTranslations(2), '0', null))) {
                        // as plural mostly in next line of the current item
                        $countIndex++;
                        $translation->translatePlural(Arr::get($translationServiceResult, (string) $countIndex, ''));
                    }
                }
                if ($isBasicTranslated) {
                    // increment for each
                    $countIndex++;
                }
            }

            //Save the translations in .po files
            $generator = new PoGenerator();
            // generate the po file
            $generator->generateFile($translations, $existingPoFile);

            //export to a .mo file:
            $generator = new MoGenerator();
            // generate mo file
            $generator->generateFile($translations, $this->getFilePath($languageId, 'mo', $domain));
            // get the response back
            if($errorMessageIfAny) {
                return $this->engineResponse(21, ['messageType' => 'warning'], __tr('Some strings failed to translate , please wait for minute and try again. Error - __errorMessage__', [
                    '__errorMessage__' => $errorMessageIfAny
                ]));
            }
            return $this->engineResponse(21, ['reloadPage' => true, 'messageType' => 'success'], __tr('Messages translated successfully.'));
        }
    }

    /**
     * Microsoft Translation Services
     *
     * @param string $languageId
     * @param array $data
     * @see https://docs.microsoft.com/en-us/azure/cognitive-services/translator/reference/v3-0-translate
     * @return void
     */
    protected function microsoftTranslate(string $languageId, array $data): array
    {
        // get first 2 characters of language id
        $languageId = substr($languageId, 0, 2);
        // get the key
        $key = getAppSettings("microsoft_translator_api_key");
        $region = getAppSettings("microsoft_translator_api_region") ?: null;
        // check if it exists
        if (!$key) {
            throw new Exception("Microsoft Translation Subscription key is missing.");
        }
        // check of data is not empty
        if (empty($data)) {
            return [];
        }
        $errorMessage = '';
        // decoded result container
        $resultDecoded = [];
        // Microsoft has limit of 1000 translation per request
        foreach (array_chunk($data, 990) as $dataItem) {
            // request translation service
            // https://docs.microsoft.com/en-us/azure/cognitive-services/translator/reference/v3-0-translate
            try {
                $result = Http::acceptJson()->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $key,
                    'Ocp-Apim-Subscription-Region' => $region,
                ])->post("https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&from=en&to={$languageId}", $dataItem)->throw()->json();
            } catch (\Throwable $th) {
                if(empty($resultDecoded)) {
                    throw $th;
                } else {
                    $errorMessage = $th->getMessage();
                    break;
                }
            }
            // merge the chunked result into container
            $resultDecoded = array_merge($resultDecoded, $result);
            if (isset($resultDecoded['error'])) {
                if(empty($resultDecoded)) {
                    // in case of errors
                    throw new Exception(Arr::get($resultDecoded, 'error.message', 'Something went wrong'));
                } else {
                    $errorMessage = Arr::get($resultDecoded, 'error.message', 'Something went wrong');
                    break;
                }
            }
        }
        // refined result container
        $refinedResultContainer = [];
        // if decoded result container has data
        if (!empty($resultDecoded)) {
            // go through each
            foreach ($resultDecoded as $resultItem) {
                // set the translation in to array
                $refinedResultContainer[] = Arr::get($resultItem, 'translations.0.text', '');
            }
        }
        // get back the result array
        return [
            'error_message' => $errorMessage,
            'translations' => $refinedResultContainer
        ];
    }
}
