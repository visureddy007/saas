(function (window, undefined) {

    'use strict';
    /**
     * __InputSecurity : 28 NOV 2019
     * LivelyWorks
     *
     *-------------------------------------------------------- */

    window.__InputSecurity = {
        /**
         *
         * Decrypt string using RSA with Public Key
         *
         * @return object
         *-------------------------------------------------------- */
        rsaDecrypt: function (encryptedString) {
            return RSA.decrypt(encryptedString, __InputSecurity.getPublicRSA());
        },

        /**
         *
         * Encrypt string using RSA with Public Key
         *
         * @return object
         *-------------------------------------------------------- */
        rsaEncrypt: function (plainString) {
            return RSA.encrypt(plainString, __InputSecurity.getPublicRSA());
        },

        /**
         *
         * get security token
         *
         * @return string
         *-------------------------------------------------------- */
        getPublicRSA: function () {
            return RSA.getPublicKey(
                window.__pbkey ? window.__pbkey : "-----BEGIN PUBLIC KEY-----MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAPJwwNa//eaQYxkNsAODohg38azVtalEh7Lw4wxlBrbDONgYaebgscpjPRloeL0kj4aLI462lcQGVAxhyh8JijsCAwEAAQ==-----END PUBLIC KEY-----"
            );
        },

        /**
         * Process encrypted data
         *
         * @return void
         *-------------------------------------------------------- */

        processSecuredData: function (responseData) {
            if (!responseData || !responseData["__maskedData"]) {
                return false;
            } else {
                var splitedValues = responseData["__maskedData"].split("__==__");
                var splitedValueString = "";
                for (var i = 0; i < splitedValues.length; i++) {
                    if (splitedValues[i]) {
                        splitedValueString += __InputSecurity.rsaDecrypt(
                            splitedValues[i]
                        );
                    }
                }
                return JSON.parse(splitedValueString);
            }
        },

        /**
         * process response data whatever it is secured or not returns decrypted data
         *
         * @return void
         *-------------------------------------------------------- */

        processResponseData: function (responseData) {
            var processedData = __InputSecurity.processSecuredData(responseData);
            if (processedData == false) {
                return responseData;
            } else {
                return processedData;
            }
        },

        processFormFields: function (dataObj, options) {
            if (dataObj && !_.isEmpty(dataObj)) {
                var newDataObj = {};
                if (!options) {
                    options = {};
                }
                options.secured = true;
                if (options && options.secured == true) {
                    _.forEach(dataObj, function (value, key) {
                        var encryptedKey = __InputSecurity.rsaEncrypt(
                            key
                        );
                        if (
                            (!options.unsecuredFields ||
                                _.includes(options.unsecuredFields, key) ===
                                false) &&
                            (_.isArray(value) === true ||
                                _.isObject(value) === true)
                            && (_.isEmpty(value) === true)
                        ) {
                            newDataObj[encryptedKey] = value;
                        } else if (
                            (!options.unsecuredFields ||
                                _.includes(options.unsecuredFields, key) ===
                                false) &&
                            (_.isArray(value) === true ||
                                _.isObject(value) === true )
                            && (_.isEmpty(value) === false)
                        ) {
                            newDataObj[encryptedKey] = __InputSecurity.processFormFields(
                                value
                            );
                        } else if (
                            (!options.unsecuredFields ||
                                _.includes(options.unsecuredFields, key) ===
                                false) &&
                            _.isArray(value) !== true &&
                            _.isObject(value) !== true
                        ) {
                            if (value || value == false) {
                                if (_.isBoolean(value) || _.isNumber(value)) {
                                    value = String(value);
                                }

                                var securedValue = __InputSecurity.rsaEncrypt(
                                    value
                                );
                                // if cannot be encrypt may long a long string and needs to be concat.
                                if (securedValue == false) {
                                    var splitedValues = value.match(/.{1,30}/g),
                                        splitedValueString = "";

                                    for (var i = 0; i < splitedValues.length; i++) {
                                        var securedSplitedValue = __InputSecurity.rsaEncrypt(
                                            splitedValues[i]
                                        );

                                        if (securedSplitedValue == false) {
                                            throw "Encryption Failed for { " +
                                            key +
                                            " } VALUE due to length";

                                            splitedValueString = false;
                                            break;
                                        } else {
                                            splitedValueString =
                                                splitedValueString +
                                                securedSplitedValue +
                                                "__==__";
                                        }
                                    }

                                    securedValue = splitedValueString;
                                }

                                // var securedKey = __InputSecurity.rsaEncrypt(key);
                                if (encryptedKey == false) {
                                    throw "Encryption Failed for { " +
                                    encryptedKey +
                                    " } KEY due to length";
                                }

                                newDataObj[encryptedKey] = securedValue;
                            }
                        } else {
                            newDataObj[key] = value;
                        }
                    });
                } else {
                    newDataObj = dataObj;
                }
            }

            return newDataObj;
        }
    };
})(window);