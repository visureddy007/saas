<?php

namespace App\Yantrana\Services\YesFileStorage;

/**
 * YesFileStorage
 *
 * Angulara File Storage System based on Laravel Storage
 *
 *--------------------------------------------------------------------------- */

use Exception;
use Illuminate\Http\File;
use Storage;

/**
 * This YesAuthority class.
 *---------------------------------------------------------------- */
class YesFileStorage // extends Filesystem
{
    /*
        Store public routes
    */
    protected $storageInstance;

    /*
        Storage Path
    */
    protected $storagePath;

    /*
        Storage Mirror Methods
    */
    protected $mirrorMethods = [
        // used to copy an existing file to a new location on the disk
        // - copyFile($from, $to)
        'copyFile' => 'copy',
        // used to rename or move an existing file to a new location
        // - moveFile($from, $to)
        'moveFile' => 'move',
        // Get the mime-type of a given file.
        // - getMimeType($path)
        'getMimeType' => 'mimeType',
        // returns the UNIX timestamp of the last time the file was modified
        // - fileModifiedAt($path)
        'fileModifiedAt' => 'lastModified',
        // array of all the directories within a given directory
        // - getFiles($directory = null, $recursive = false)
        'getFiles' => 'files',
        // to get a list of all directories within a given directory and all of its sub-directories
        // - getAllFiles($directory = null)
        'getAllFiles' => 'allFiles',
        // array of all the directories within a given directory
        // - getFolders($directory = null, $recursive = false)
        'getFolders' => 'directories',
        // method to get a list of all directories within a given directory and all of its sub-directories
        // - getAllFolders($directory = null)
        'getAllFolders' => 'allDirectories',
        // will create the given directory, including any needed sub-directories
        // - createFolder($path)
        'createFolder' => 'makeDirectory',
        // may be used to remove a directory and all of its files
        // - deleteFolder($directory)
        'deleteFolder' => 'deleteDirectory',
        // accepts a single filename or an array of files to remove from the disk
        // - delete($path) or delete([$path])
        'deleteFile' => 'delete',
        // Write the contents of a file.
        // -  writeFile($path, $contents, $options = [])
        'writeFile' => 'put',
        // Get the contents of a file.
        // - getFile($path)
        'getFile' => 'get',
        // allow you to write to the beginning of a file
        // - filePrepend($path, $data, $separator = PHP_EOL)
        'filePrepend' => 'prepend',
        // allow you to write to the end of a file
        // - fileAppend($path, $data, $separator = PHP_EOL)
        'fileAppend' => 'append',
        //  visibility can be retrieved
        // - getFileAccessType($path)
        'getFileAccessType' => 'getVisibility',
        //  visibility can be set
        // - setFileAccessType($path, $visibility) // public or private
        'setFileAccessType' => 'setVisibility',
        'isExists' => 'exists',
    ];

    /**
     * Constructor
     *
     *
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        $this->storageInstance = Storage::disk();
    }

    /**
     * Call the methods from Storage
     *
     *
     * @return void
     *-----------------------------------------------------------------------*/
    public function __call($func, $params)
    {
        if (array_key_exists($func, $this->mirrorMethods)) {
            return call_user_func_array([
                $this->storageInstance,
                $this->mirrorMethods[$func],
            ], $params);
        }

        $flipedMirrorMethods = array_flip($this->mirrorMethods);

        // mirror method suggetions
        if (array_key_exists($func, $flipedMirrorMethods)) {
            throw new Exception('Undefined method - '.$func.' instead use '.$flipedMirrorMethods[$func]);
        }

        // not registered method
        throw new Exception('Undefined method - '.$func, 2);
    }

    /**
     * Select Disk
     *
     * @return void
     *-----------------------------------------------------------------------*/
    public function on($selectDisk)
    {
        $this->storageInstance = Storage::disk($selectDisk);
        $getAdapter = $this->storageInstance->getAdapter();
        if (method_exists($getAdapter, 'disconnect')) {
            $getAdapter->disconnect();
        }

        if (method_exists($getAdapter, 'connect')) {
            $getAdapter->connect();
        }

        return $this;
    }

    /**
     * Store the uploaded file on the disk.
     *
     * @param  string  $path
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile  $file
     * @param  array  $options
     * @return string|false
     */
    public function storeFile($path, $file, $options = [])
    {
        $lastItemOfPath = last(
            explode('/', $path)
        );
        if (str_contains($lastItemOfPath, '.')) {
            return $this->storeFileAs(str_replace('/'.$lastItemOfPath, '', $path), $file, $lastItemOfPath, $options);
        }

        if ($file instanceof Illuminate\Http\UploadedFile) {
            return $this->storageInstance->putFile($path, $file, $options);
        }

        return $this->storageInstance->putFile($path, new File($file), $options);
    }

    /**
     * Store the uploaded file on the disk with a given name.
     *
     * @param  string  $path
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile  $file
     * @param  string  $name
     * @param  array  $options
     * @return string|false
     *-----------------------------------------------------------------------*/
    public function storeFileAs($path, $file, $name, $options = [])
    {

        // uploaded files
        if ($file instanceof Illuminate\Http\UploadedFile) {
            return $this->storageInstance->putFile($path, $file, $options);
        }

        return $this->storageInstance->putFileAs($path, new File($file), $name, $options);
    }

    /**
     * Determine if a file not exists
     * then Copy a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function copyIfNotExist($from, $to)
    {
        if (! $this->storageInstance->exists($to)) {
            return $this->storageInstance->copy($from, $to);
        }

        return false;
    }

    /**
     * Get the contents of a file if exists.
     *
     * @param  string  $path
     * @return string|bool
     */
    public function getFileIfExists($path)
    {
        if ($this->storageInstance->exists($path)) {
            return $this->storageInstance->get($path);
        }

        return false;
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     */
    public function getUrl($path)
    {
        if ($this->storageInstance->exists($path)) {
            return $this->storageInstance->url($path);
        }

        return null;
    }

    /**
     * Get the URL for the file by key.
     *
     * @param  string  $path
     * @return string
     */
    public function getUrlByKey($key, $dynamicItems = null, $filename = '')
    {
        return $this->getUrl(
            getPathByKey($key, $dynamicItems).($filename ? '/'.$filename : '')
        );
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface / number $expiration
     * @return string|bool
     */
    public function getTempUrl($path, $expiration = 5, array $options = [])
    {
        if (is_numeric($expiration)) {
            $expiration = now()->addMinutes($expiration);
        }

        if ($this->storageInstance->exists($path)) {
            return $this->storageInstance->temporaryUrl($path, $expiration, $options);
        }

        return null;
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface / number $expiration
     * @return string|bool
     */
    public function getTempUrlByKey($key, $dynamicItems = null, $filename = '', $expiration = 5, array $options = [])
    {
        return $this->getTempUrl(
            getPathByKey($key, $dynamicItems).($filename ? '/'.$filename : ''),
            $expiration,
            $options
        );
    }

    /**
     * Create a streamed download response for a given file.
     *
     * @param  string  $path
     * @param  string|null  $name
     * @param  array|null  $headers
     * @return \Symfony\Component\HttpFoundation\StreamedResponse | false if file not exists
     */
    public function downloadFile($path, $name = null, array $headers = [])
    {
        // check if file exists
        if ($this->storageInstance->exists($path)) {
            // check if has extension
            if ($name and ! str_contains($name, '.')) {
                // grab the extension
                $extension = $this->extension($path);
                // if extension found append it
                if ($extension) {
                    $name = $name.'.'.$extension;
                }
            }

            // call for download
            return $this->storageInstance->download($path, $name, $headers);
        }

        return false;
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int|string
     */
    public function getSize($path, bool $formatted = true)
    {
        $fileSize = $this->storageInstance->size($path);
        if ($formatted) {
            $fileSize = $this->formatSizeUnits($fileSize);
        }

        return $fileSize;
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function getStoragePath($path = '')
    {
        return $this->storagePath.$path;
    }

    /**
     * Units conversion
     *
     * @param  string  $path
     * @return string
     */
    // Snippet from PHP Share: http://www.phpshare.org
    // https://stackoverflow.com/questions/5501427/php-filesize-mb-kb-conversion
    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2).' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
