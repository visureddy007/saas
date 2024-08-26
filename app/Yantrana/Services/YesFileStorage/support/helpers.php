<?php

/**
 * YesFileStorage Helpers.
 *
 * Common helper functions for YesFileStorage applications
 *
 *-------------------------------------------------------- */
/*
      * Get Path by key
      *
      * @return string path.
      *-------------------------------------------------------- */

if (! function_exists('getPathByKey')) {
    function getPathByKey($item, $dynamicItems = null)
    {
        $storagePaths = config('yes-file-storage.storage_paths');

        if (! $storagePaths || empty($storagePaths)) {
            throw new Exception('storage_paths not defined in config/yes-file-storage.php', 1);
        }

        $storagePaths = __nestedKeyValues($storagePaths, '/');

        $itemPath = array_get($storagePaths, $item, null);

        if (! $itemPath) {
            throw new Exception("key@$item not found in storage_paths", 1);
        }

        if ($itemPath) {
            if ($dynamicItems and is_array($dynamicItems)) {
                $itemPath = strtr($itemPath, $dynamicItems);
            }

            return cleanPath($itemPath);
        }

        return null;
    }
}

if (! function_exists('getTempUploadedFile')) {
    function getTempUploadedFile($item = '')
    {
        return public_path(getPathByKey('user_temp_uploads', [
            '{_uid}' => authUID()
        ]) . DIRECTORY_SEPARATOR . $item);
    }
}
if (! function_exists('deleteTempUploadedFile')) {
    function deleteTempUploadedFile($item)
    {
       try {
        return unlink(public_path(getPathByKey('user_temp_uploads', [
            '{_uid}' => authUID()
        ]) . DIRECTORY_SEPARATOR . $item));
       } catch (\Throwable $th) {
        return false;
       }
    }
}
