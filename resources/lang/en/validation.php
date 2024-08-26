<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => __tr('The :attribute must be accepted.'),
    'active_url' => __tr('The :attribute is not a valid URL.'),
    'after' => __tr('The :attribute must be a date after :date.'),
    'after_or_equal' => __tr('The :attribute must be a date after or equal to :date.'),
    'alpha' => __tr('The :attribute may only contain letters.'),
    'alpha_dash' => __tr('The :attribute may only contain letters, numbers, dashes and underscores.'),
    'alpha_num' => __tr('The :attribute may only contain letters and numbers.'),
    'array' => __tr('The :attribute must be an array.'),
    'before' => __tr('The :attribute must be a date before :date.'),
    'before_or_equal' => __tr('The :attribute must be a date before or equal to :date.'),
    'between' => [
        'numeric' => __tr('The :attribute must be between :min and :max.'),
        'file' => __tr('The :attribute must be between :min and :max kilobytes.'),
        'string' => __tr('The :attribute must be between :min and :max characters.'),
        'array' => __tr('The :attribute must have between :min and :max items.'),
    ],
    'boolean' => __tr('The :attribute field must be true or false.'),
    'confirmed' => __tr('The :attribute confirmation does not match.'),
    'date' => __tr('The :attribute is not a valid date.'),
    'date_equals' => __tr('The :attribute must be a date equal to :date.'),
    'date_format' => __tr('The :attribute does not match the format :format.'),
    'different' => __tr('The :attribute and :other must be different.'),
    'digits' => __tr('The :attribute must be :digits digits.'),
    'digits_between' => __tr('The :attribute must be between :min and :max digits.'),
    'dimensions' => __tr('The :attribute has invalid image dimensions.'),
    'distinct' => __tr('The :attribute field has a duplicate value.'),
    'email' => __tr('The :attribute must be a valid email address.'),
    'ends_with' => __tr('The :attribute must end with one of the following: :values.'),
    'exists' => __tr('The selected :attribute is invalid.'),
    'file' => __tr('The :attribute must be a file.'),
    'filled' => __tr('The :attribute field must have a value.'),
    'gt' => [
        'numeric' => __tr('The :attribute must be greater than :value.'),
        'file' => __tr('The :attribute must be greater than :value kilobytes.'),
        'string' => __tr('The :attribute must be greater than :value characters.'),
        'array' => __tr('The :attribute must have more than :value items.'),
    ],
    'gte' => [
        'numeric' => __tr('The :attribute must be greater than or equal :value.'),
        'file' => __tr('The :attribute must be greater than or equal :value kilobytes.'),
        'string' => __tr('The :attribute must be greater than or equal :value characters.'),
        'array' => __tr('The :attribute must have :value items or more.'),
    ],
    'image' => __tr('The :attribute must be an image.'),
    'in' => __tr('The selected :attribute is invalid.'),
    'in_array' => __tr('The :attribute field does not exist in :other.'),
    'integer' => __tr('The :attribute must be an integer.'),
    'ip' => __tr('The :attribute must be a valid IP address.'),
    'ipv4' => __tr('The :attribute must be a valid IPv4 address.'),
    'ipv6' => __tr('The :attribute must be a valid IPv6 address.'),
    'json' => __tr('The :attribute must be a valid JSON string.'),
    'lt' => [
        'numeric' => __tr('The :attribute must be less than :value.'),
        'file' => __tr('The :attribute must be less than :value kilobytes.'),
        'string' => __tr('The :attribute must be less than :value characters.'),
        'array' => __tr('The :attribute must have less than :value items.'),
    ],
    'lte' => [
        'numeric' => __tr('The :attribute must be less than or equal :value.'),
        'file' => __tr('The :attribute must be less than or equal :value kilobytes.'),
        'string' => __tr('The :attribute must be less than or equal :value characters.'),
        'array' => __tr('The :attribute must not have more than :value items.'),
    ],
    'max' => [
        'numeric' => __tr('The :attribute may not be greater than :max.'),
        'file' => __tr('The :attribute may not be greater than :max kilobytes.'),
        'string' => __tr('The :attribute may not be greater than :max characters.'),
        'array' => __tr('The :attribute may not have more than :max items.'),
    ],
    'mimes' => __tr('The :attribute must be a file of type: :values.'),
    'mimetypes' => __tr('The :attribute must be a file of type: :values.'),
    'min' => [
        'numeric' => __tr('The :attribute must be at least :min.'),
        'file' => __tr('The :attribute must be at least :min kilobytes.'),
        'string' => __tr('The :attribute must be at least :min characters.'),
        'array' => __tr('The :attribute must have at least :min items.'),
    ],
    'multiple_of' => __tr('The :attribute must be a multiple of :value'),
    'not_in' => __tr('The selected :attribute is invalid.'),
    'not_regex' => __tr('The :attribute format is invalid.'),
    'numeric' => __tr('The :attribute must be a number.'),
    'password' => __tr('The password is incorrect.'),
    'present' => __tr('The :attribute field must be present.'),
    'regex' => __tr('The :attribute format is invalid.'),
    'required' => __tr('The :attribute field is required.'),
    'required_if' => __tr('The :attribute field is required when :other is :value.'),
    'required_unless' => __tr('The :attribute field is required unless :other is in :values.'),
    'required_with' => __tr('The :attribute field is required when :values is present.'),
    'required_with_all' => __tr('The :attribute field is required when :values are present.'),
    'required_without' => __tr('The :attribute field is required when :values is not present.'),
    'required_without_all' => __tr('The :attribute field is required when none of :values are present.'),
    'same' => __tr('The :attribute and :other must match.'),
    'size' => [
        'numeric' => __tr('The :attribute must be :size.'),
        'file' => __tr('The :attribute must be :size kilobytes.'),
        'string' => __tr('The :attribute must be :size characters.'),
        'array' => __tr('The :attribute must contain :size items.'),
    ],
    'starts_with' => __tr('The :attribute must start with one of the following: :values.'),
    'string' => __tr('The :attribute must be a string.'),
    'timezone' => __tr('The :attribute must be a valid zone.'),
    'unique' => __tr('The :attribute has already been taken.'),
    'uploaded' => __tr('The :attribute failed to upload.'),
    'url' => __tr('The :attribute format is invalid.'),
    'uuid' => __tr('The :attribute must be a valid UUID.'),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'confirmation_code' => [
            'captcha' => __tr('The confirmation code is invalid.'),
            'attribute-name' => [
                'rule-name' => 'custom-message',
            ],
            'email' => [
                'unique_email' => __tr('The __attribute__ has already been taken.', [
                    '__attribute__' => ':attribute',
                ]),
                'unique_in_email_change_request' => __tr('The __attribute__ has already been taken for new email request.', [
                    '__attribute__' => ':attribute',
                ]),
                'unique_client_member_email' => __tr('The __attribute__ has already been taken.', [
                    '__attribute__' => ':attribute',
                ]),
            ],
            'new_email' => [
                'unique_email' => __tr('The __attribute__ has already been taken.', [
                    '__attribute__' => ':attribute',
                ]),
            ],
            'custom_domain' => [
                'domain' => __tr('The __attribute__ format is invalid.', [
                    '__attribute__' => ':attribute',
                ]),
            ],
            'comment' => [
                'verify_comment' => __tr('The __attribute__ field is required.', [
                    '__attribute__' => ':attribute',
                ]),
            ],
        ],

        'reactions' => [
            14 => [
                __tr('Ooops... No changes made!!'),
                __tr('Ooops... Nothing to process!!'),
                __tr("It seems you didn't modified anything!!"),
            ], // for reaction code 14
            15 => [
                __tr('Please wait files uploading in progress ...'),
                __tr('Please wait a while its in progress ...'),
            ], // for reaction code 15
            16 => __tr('Files uploaded successfully'), // for reaction code 16
            4 => [
                __tr('Oh..no...Something left invalid!!'),
                __tr('Ooops... Validation Errrrors...!!'),
                __tr('Oh... it looks invalid...!!'),
                __tr('Something went wrong with validation!!'),
            ], // for validation error
            1 => __tr('Request processed successfully'), // for request success
            6 => __tr('Invalid request access'), // for invalid request
            19 => __tr('Oooops ... something went wrong on server. Try after some time!!'), // for request success
            20 => [
                __tr('Invalid request token. Please reload page and try again!!'),
                __tr('Request token Expired. Please reload page and try again!!'),
                'attribute-name' => [
                    'rule-name' => 'custom-message',
                ],
            ],

            /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

            'attributes' => [],
        ],
    ],
];
