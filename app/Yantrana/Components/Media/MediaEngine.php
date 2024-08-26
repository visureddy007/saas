<?php

/**
 * MediaEngine.php - Main component file
 *
 * This file is part of the Media component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Media;

use App\Yantrana\Base\BaseMediaEngine;
use App\Yantrana\Components\Media\Interfaces\MediaEngineInterface;
use Exception;
use File;
use Illuminate\Filesystem\Filesystem;
use YesFileStorage;

class MediaEngine extends BaseMediaEngine implements MediaEngineInterface
{
    protected $elements;

    protected $currentDisk;

    protected $disk;

    /**
     * Constructor.
     *
     * @param  MediaRepository  $mediaRepository  - Media Repository
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        $this->currentDisk = config('filesystems.default', 'public-media-storage'); //configItem('current_filesystem_driver');
        $this->disk = YesFileStorage::on($this->currentDisk); // do_s3_space, local
        $this->elements = config('yes-file-storage.element_config');
    }

    /**
     * Process Upload Logo
     *
     * @return array|object
     *---------------------------------------------------------------- */
    public function processUploadLogo($inputFile, $requestFor)
    {
        $logoFolderPath = getPathByKey('logo');

        return $this->processUpload($inputFile, $logoFolderPath, $requestFor);
    }

    /**
     * Process Upload Logo
     *
     * @return array|object
     *---------------------------------------------------------------- */
    public function processVendorUpload($inputFile, $requestFor, $allowedItems = [])
    {
        if (! array_key_exists($requestFor, $allowedItems)) {
            return $this->engineFailedResponse([], __tr('Invalid Request'));
        }

        $logoFolderPath = getPathByKey($requestFor, ['{_uid}' => getVendorUid()]);

        return $this->processUpload($inputFile, $logoFolderPath, $requestFor, true, getVendorSettings('logo_name'));
    }

    /**
     * Process Upload Logo
     *
     * @return EngineResponse
     *---------------------------------------------------------------- */
    public function processUploadSmallLogo($inputFile, $requestFor)
    {
        $logoFolderPath = getPathByKey('small_logo');

        return $this->processUpload($inputFile, $logoFolderPath, $requestFor);
    }

    /**
     * Process Upload Logo
     *
     * @return array|object
     *---------------------------------------------------------------- */
    public function processUploadFavicon($inputFile, $requestFor)
    {
        $logoFolderPath = getPathByKey('favicon');

        return $this->processUpload($inputFile, $logoFolderPath, $requestFor);
    }

    /**
     * Process Upload Profile Image
     *
     * @return array|object
     *---------------------------------------------------------------- */
    public function processUploadProfile($inputFile, $requestFor)
    {
        $uploadedFileOnLocalServer = $this->processUploadFileOnLocalServer($inputFile, $requestFor);

        if ($uploadedFileOnLocalServer['reaction_code'] == 1) {
            $fileName = $uploadedFileOnLocalServer['data']['fileName'];
            $profileImageFolderPath = getPathByKey('profile_photo', ['{_uid}' => authUID()]);

            return $this->resizeImageAndUpload($profileImageFolderPath, $fileName, [
                'height' => 360,
                'width' => 360,
            ]);

            return $this->engineFailedResponse([], __tr('Something went wrong while file moving.'));
        }

        return $uploadedFileOnLocalServer;
    }

    /**
     * Process Upload Profile Image
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processUploadCoverPhoto($inputFile, $requestFor)
    {
        $uploadedFileOnLocalServer = $this->processUploadFileOnLocalServer($inputFile, $requestFor);

        if ($uploadedFileOnLocalServer['reaction_code'] == 1) {
            $fileName = $uploadedFileOnLocalServer['data']['fileName'];
            $coverPhotoFolderPath = getPathByKey('cover_photo', ['{_uid}' => authUID()]);

            return $this->resizeImageAndUpload($coverPhotoFolderPath, $fileName, [
                'height' => 312,
                'width' => 820,
            ]);

            return $this->engineFailedResponse([], __tr('Something went wrong while file moving.'));
        }

        return $uploadedFileOnLocalServer;
    }

    /**
     * Process Upload Profile Image
     *
     * @return EngineResponse
     *---------------------------------------------------------------- */
    public function whatsappMediaUploadProcess($inputFile, $requestFor)
    {
        $uploadedFileOnLocalServer = $this->processUploadFileOnLocalServer($inputFile, $requestFor);

        if ($uploadedFileOnLocalServer['reaction_code'] == 1) {
            $fileName = $uploadedFileOnLocalServer['data']['fileName'];
            $itemImageFolderPath = getPathByKey($requestFor, ['{_uid}' => getVendorUid()]);

            return $this->resizeImageAndUpload($itemImageFolderPath, $fileName);
        }

        return $uploadedFileOnLocalServer;
    }

    /**
     * Download file and store
     *
     * @return array
     *---------------------------------------------------------------- */
    public function downloadAndStoreMediaFile($fileValue, $vendorUid, $mediaType = 'image')
    {
        $mimeTypesToExtension = [
            // audio
            'audio/aac' => 'aac',
            'audio/mp4' => 'm4a', // or 'mp4' if you are not distinguishing between audio-only and video
            'audio/mpeg' => 'mp3',
            'audio/amr' => 'amr',
            'audio/ogg' => 'ogg',
            // videos
            'video/mp4' => 'mp4',
            'video/3gp' => '3gp',
            // images
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            // documents
            'text/plain' => 'txt',
            'application/pdf' => 'pdf',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/msword' => 'doc',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            // Add more MIME types and their corresponding extensions as needed.
        ];
        $filesStored = [];
        try {
            $fileData = $fileValue['body'];
            if ($fileData) {
                $permanentFolderPath = getPathByKey("whatsapp_$mediaType", ['{_uid}' => $vendorUid]);
                $tempUploadFolderPath = getPathByKey('user_temp_uploads', ['{_uid}' => $vendorUid]);
                $filename = uniqid().'.'.$mimeTypesToExtension[$fileValue['mime_type']];
                // temp file storage
                $writtenFile = $this->disk->writeFile($tempUploadFolderPath.'/'.$filename, $fileData);
                // move to permanent storage
                $storedInfo = $this->processMoveFile($permanentFolderPath, $filename, [], [
                    'setVisibility' => 'public',
                    'publicMediaStorage' => false,
                    'pathParameters' => [
                        '{_uid}' => $vendorUid,
                    ],
                ]);
                $filesStored = $storedInfo->data();
            }
        } catch (Exception $e) {
            __logDebug($e->getMessage());
        }

        return $filesStored;
    }

    /**
     * Delete temp file
     *
     * @param  string  $filename
     * @return bool
     *---------------------------------------------------------------- */
    public function deleteLocalTempFile($filename)
    {
        $path = getPathByKey('user_temp_uploads', ['{_uid}' => authUID()]);

        return $this->processDeleteFile($path, $filename);
    }

    /**
     * Delete media image
     *
     * @param  number  $productID
     * @return bool
     *---------------------------------------------------------------- */
    public function processDeleteFile($destinationPath, $filename = null)
    {
        $imageMediaPath = $destinationPath.'/'.$filename;
        // Check if image media exist & is deleted successfully
        if (File::exists($imageMediaPath) and File::delete($imageMediaPath)) {
            return true;
        }

        return false;
    }

    /**
     * Delete user all account data
     *
     * @return array
     *---------------------------------------------------------------- */
    public function deleteUserVendor()
    {
        $userVendorFolderPath = getPathByKey('user', ['{_uid}' => getUserUID()]);

        return $this->disk->deleteFolder($userVendorFolderPath);
    }

    /**
     * Process Upload Logo
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processUploadTranslationFile($inputFile, $requestFor)
    {
        $logoFolderPath = getPathByKey('language_file');
        $this->disk = YesFileStorage::on('local');
        $uploadResult = $this->processUpload($inputFile, $logoFolderPath, $requestFor);
        $this->disk = YesFileStorage::on($this->currentDisk);

        return $uploadResult;
    }
    /**
     * Process Import Contacts
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processUploadImportContactFile($inputFile)
    {
        $logoFolderPath = getPathByKey('vendor_contact_import');
        $this->disk = YesFileStorage::on('local');
        $uploadResult = $this->processUpload($inputFile, $logoFolderPath, 'vendor_contact_import');
        $this->disk = YesFileStorage::on($this->currentDisk);
        return $uploadResult;
    }

    /**
     * Delete older files
     *
     * @param  string  $dir
     * @param  int  $max_age  - default is 24 hours
     * @return void
     */
    public function deleteOldFiles($dir, $max_age = 3600) // 1 hours
    {
        $list = [];

        $limit = time() - $max_age;

        $dir = realpath($dir);

        if (! is_dir($dir)) {
            return;
        }

        $dh = opendir($dir);
        if ($dh === false) {
            return;
        }

        while (($file = readdir($dh)) !== false) {
            $file = $dir.'/'.$file;
            if (! is_file($file)) {
                continue;
            }

            if (filemtime($file) < $limit) {
                $list[] = $file;
                unlink($file);
            }
        }
        closedir($dh);

        return $list;
    }

    /**
     * Process Upload Profile Image
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processUploadProfilePicture($inputFile, $requestFor, $pathValues = [])
    {
        $uploadedFileOnLocalServer = $this->processUploadFileOnLocalServer($inputFile, $requestFor);
        if ($uploadedFileOnLocalServer['reaction_code'] == 1) {
            $fileName = $uploadedFileOnLocalServer['data']['fileName'];
            $profileImageFolderPath = getPathByKey('profile_picture', $pathValues);

            return $this->resizeImageAndUpload($profileImageFolderPath, $fileName, [
                'height' => 360,
                'width' => 360,
            ]);

            return $this->engineFailedResponse([], __tr('Something went wrong while file moving.'));
        }

        return $uploadedFileOnLocalServer;
    }

    /**
     * Common Process Upload Image
     *
     * @return array
     *---------------------------------------------------------------- */
    public function processUploadedFile($inputFile, $requestFor, $pathValues = [], $options = [])
    {
        $options = array_merge([
            'resize' => null,
        ], $options);
        $uploadedFileOnLocalServer = $this->processUploadFileOnLocalServer($inputFile, $requestFor);
        if ($uploadedFileOnLocalServer['reaction_code'] == 1) {
            $fileName = $uploadedFileOnLocalServer['data']['fileName'];
            $uploadedItemFolderPath = getPathByKey($requestFor, $pathValues);

            $processReaction = $this->resizeImageAndUpload($uploadedItemFolderPath, $fileName);
            if ($processReaction['reaction_code'] == 1) {
                return $this->engineSuccessResponse([
                    'folder_path' => $uploadedItemFolderPath,
                    'file_name' => $fileName,
                    'file_url' => getMediaUrl($uploadedItemFolderPath, $fileName),
                    'file_path' => $uploadedItemFolderPath.DIRECTORY_SEPARATOR.$fileName,
                ], __tr('File Uploaded Successfully'));
            }

            return $this->engineFailedResponse([], __tr('Something went wrong while file moving.'));
        }

        return $uploadedFileOnLocalServer;
    }

   

     
}
