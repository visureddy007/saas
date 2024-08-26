<?php

/**
 *  YesFileStorage Configurations
 *
 *  This configuration file is part of YesFileStorage
 *
 *------------------------------------------------------------------------------------------------*/

return [

    /**  Restrictions for elements to be uploaded
     *--------------------------------------------------------------------------------------------*/
    'element_config' => [
        'all' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/jpg', 'image/JPG', 'image/jpeg', 'image/JPEG'],
                'allowedFileExtensions' => ['jpg','png','jpeg'],
            ],
        ],
        'logo' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/png', 'image/PNG', 'image/svg', 'image/svg+xml'],
                'allowedFileExtensions' => ['png','svg'],
            ],
        ],
        'small_logo' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/png', 'image/PNG', 'image/svg', 'image/svg+xml'],
                'allowedFileExtensions' => ['png','svg'],
            ],
        ],
        'favicon' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/ico', 'image/vnd.microsoft.icon', 'image/png', 'image/PNG'],
                'allowedFileExtensions' => ['png','ico'],
            ],
        ],
        'profile' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/jpg', 'image/JPG', 'image/jpeg', 'image/JPEG', 'image/png', 'image/gif', 'image/svg', 'image/svg+xml'],
                'allowedFileExtensions' => ['jpg','png','jpeg','svg','gif'],
            ],
        ],
        'cover_image' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/jpg', 'image/JPG', 'image/jpeg', 'image/JPEG', 'image/png'],
                'allowedFileExtensions' => ['jpg','png','jpeg'],
            ],
        ],
        'photos' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/jpg', 'image/JPG', 'image/jpeg', 'image/JPEG', 'image/png', 'image/gif', 'image/svg', 'image/svg+xml'],
                'allowedFileExtensions' => ['jpg','png','jpeg','svg','gif'],
            ],
        ],
        'language' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                'allowedFileExtensions' => ['xlsx'],
            ],
        ],
        'whatsapp_image' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/jpeg', 'image/png'],
                'allowedFileExtensions' => ['jpg','png','jpeg'],
            ],
        ],
        'whatsapp_video' => [
            'restrictions' => [
                'allowedFileTypes' => ['video/mp4', 'video/3gp'],
                'allowedFileExtensions' => ['mp4','3gp'],
            ],
        ],
        'whatsapp_audio' => [
            'restrictions' => [
                'allowedFileTypes' => [
                    'audio/aac',
                    'audio/mp4',
                    'audio/mpeg',
                    'audio/amr',
                    'audio/ogg',
                ],
                'allowedFileExtensions' => ['aac','m4a', 'mp4', 'mp3','mpga', 'amr', 'ogg', 'oga'],
            ],
        ],
        'whatsapp_document' => [
            'restrictions' => [
                'allowedFileTypes' => [
                    'text/plain', 'application/pdf',
                    'application/vnd.ms-powerpoint',
                    'application/msword',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ],
                'allowedFileExtensions' => ['txt','pdf', 'ppt', 'pps','doc', 'xls', 'docx', 'xlsx', 'pptx'],
            ],
        ],
        'vendor_logo' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/png', 'image/PNG', 'image/svg', 'image/svg+xml'],
                'allowedFileExtensions' => ['png','svg'],
            ],
        ],
        'vendor_favicon' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/ico', 'image/vnd.microsoft.icon', 'image/png', 'image/PNG'],
                'allowedFileExtensions' => ['png','ico'],
            ],
        ],
        'vendor_contact_import' => [
            'restrictions' => [
                'allowedFileTypes' => ['image/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                'allowedFileExtensions' => ['xlsx'],
            ],
        ],
    ],

    /** Uploaded media path
     *--------------------------------------------------------------------------------------------*/
    'storage_paths' => [
        'temp' => 'key@language_file', // it will be in local storage
        env('STORAGE_BASE_FOLDER', '').'media-storage' => [
            'users-temp-uploads' => [
                '{_uid}' => [
                    'temp_uploads' => 'key@user_temp_uploads',
                ],
            ],
            'logo' => 'key@logo',
            'small_logo' => 'key@small_logo',
            'favicon' => 'key@favicon',
            'vendors' => [
                '{_uid}' => [
                    'logo' => 'key@vendor_logo',
                    'small_logo' => 'key@vendor_small_logo',
                    'favicon' => 'key@vendor_favicon',
                    'whatsapp_media' => [
                        'images' => 'key@whatsapp_image',
                        'videos' => 'key@whatsapp_video',
                        'documents' => 'key@whatsapp_document',
                        'audios' => 'key@whatsapp_audio',
                    ],
                ],
            ],
            'users' => [
                '{_uid}' => [
                    '' => 'key@user',
                    // 'temp_uploads' => 'key@user_temp_uploads',
                    'profile' => 'key@profile_photo',
                    'cover' => 'key@cover_photo',
                    'photos' => 'key@user_photos',
                ],
            ],
        ],
    ],
];
