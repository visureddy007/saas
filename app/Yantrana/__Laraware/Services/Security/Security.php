<?php

namespace App\Yantrana\__Laraware\Services\Security;

use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;

/**
 * Security - 0.6.2 - 08 AUG 2018.
 *
 *
 * Dependencies:
 *
 * Laravel     5.0 +     - http://laravel.com
 *-------------------------------------------------------- */
class Security
{
    /**
     * AES encryption type.
     *------------------------------------------------------------------------ */
    protected $aesEncryptionType = 'aes-256-cbc';

    /**
     * Security ID for form encryption.
     *------------------------------------------------------------------------ */
    protected $formSecurityID = '__yes_security';

    /*
        NOTES: Generating RSA Keys example:

        $ openssl genrsa -out rsa_1024_priv.pem 1024
        $ openssl rsa -pubout -in rsa_1024_priv.pem -out rsa_1024_pub.pem

        ---------- OR ------------

        $ openssl genrsa -out rsa_aes256_priv.pem -aes256
        $ openssl rsa -pubout -in rsa_aes256_priv.pem -out rsa_aes256_pub.pem

     */

    /**
     * Get security encryption token.
     *
     * @return string
     *------------------------------------------------------------------------ */
    public function token()
    {
        return csrf_token();
    }

    /**
     * Get security encryption getFormSecurityID.
     *
     * @return string
     *------------------------------------------------------------------------ */
    public function getFormSecurityID()
    {
        return $this->formSecurityID;
    }

    /**
     * Get RSA passphrse/password.
     *
     * @return string
     *------------------------------------------------------------------------ */
    protected function getRsaPassphrse()
    {
        return config(
            '__tech.form_encryption.default_rsa_passphrase',
            config('tech-config.form_encryption.default_rsa_passphrase')
        );
    }

    /**
     * Get RSA public key.
     *
     * @return string
     *------------------------------------------------------------------------ */
    public function getPublicRsaKey()
    {
        return config(
            '__tech.form_encryption.default_rsa_public_key',
            config('tech-config.form_encryption.default_rsa_public_key')
        );
    }

    /**
     * Get RSA private key.
     *
     * @return string
     *------------------------------------------------------------------------ */
    protected function getPrivateRsaKey()
    {
        return config(
            '__tech.form_encryption.default_rsa_private_key',
            config('tech-config.form_encryption.default_rsa_private_key')
        );
    }

    /**
     * Decrypt RSA with Private key.
     *
     * @param  mixed  $encryptedString
     * @return mixed
     *------------------------------------------------------------------------ */
    public function decryptRSA($encryptedString)
    {
        if (openssl_private_decrypt(
            base64_decode($encryptedString),
            $decryptedData,
            openssl_pkey_get_private(
                $this->getPrivateRsaKey(),
                $this->getRsaPassphrse()
            )
        )) {
            return $decryptedData;
        }
    }

    /**
     * Decrypt RSA with Private key for long strings.
     *
     * @param  mixed  $encryptedString
     * @return mixed
     *------------------------------------------------------------------------ */
    public function decryptLongRSA($encryptedString)
    {
        $descriptedValue = $this->decryptRSA($encryptedString);
        // if cannot be decrypt may long concated string are there
        if (! $descriptedValue) {
            // make parts of it via specified string chars
            $splitedValues = explode('__==__', $encryptedString);
            $splitedString = '';

            foreach ($splitedValues as $splitedValue) {
                $splitedString .= $this->decryptRSA($splitedValue);
                // if cannot be decrypt need to stop and set original decrypted value
                if (! $splitedString) {
                    $splitedString = $descriptedValue;
                    break;
                }
            }

            $descriptedValue = $splitedString;
        }

        return $descriptedValue;
    }

    /**
     * Encrypt RSA with Private key.
     *
     * @param  mixed  $plainString
     * @return mixed
     *------------------------------------------------------------------------ */
    public function encryptRSA($plainString)
    {
        if (openssl_private_encrypt(
            $plainString,
            $crypted,
            openssl_pkey_get_private(
                $this->getPrivateRsaKey(),
                $this->getRsaPassphrse()
            )
        )) {
            return base64_encode($crypted);
        }
    }

    /**
     * Encrypt RSA with Private key for long strings.
     *
     * @param  mixed  $plainData
     * @return mixed
     *------------------------------------------------------------------------ */
    public function encryptLongRSA($plainData)
    {
        $jsonStringsCollection = str_split(
            json_encode(
                $plainData
            ),
            30
        );

        $encryptedString = '';

        foreach ($jsonStringsCollection as $value) {
            $encryptedString .= ($this->encryptRSA($value).'__==__');
        }

        return $encryptedString;
    }

    /*--------------------------------------------------------------------------
    * NOTES - 11 JUL 2015
    * Modified version for CryptoJS AES encryption/decryption originally from
    * BrainFooLong (bfldev.com)
    * https://github.com/brainfoolong/cryptojs-aes-php
    * Allow you to use AES encryption on client side and server side vice versa
    *
    -------------------------------------------------------------------------*/

    /**
     * Adapted from
     * Encrypt value to a cryptojs compatiable json encoding string.
     *
     * @param  mixed  $passphrase
     * @param  mixed  $value
     * @return string
     *---------------------------------------------------------------- */
    public function encryptCryptoJsAES($plainString, $passphrase = false)
    {
        if (! $passphrase) {
            $passphrase = $this->token();
        }

        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';

        while (strlen($salted) < 48) {
            $dx = md5($dx.$passphrase.$salt, true);
            $salted .= $dx;
        }

        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($plainString), $this->aesEncryptionType, $key, true, $iv);
        $data = ['ct' => base64_encode($encrypted_data), 'iv' => bin2hex($iv), 's' => bin2hex($salt)];

        return base64_encode(json_encode($data));
    }

    /**
     * Decrypt data from a CryptoJS json encoding string.
     *
     * @param  mixed  $passphrase
     * @param  mixed  $encryptedString
     * @return mixed
     *------------------------------------------------------------------------ */
    public function decryptCryptoJsAES($encryptedString, $passphrase = false)
    {
        if (! $passphrase) {
            $passphrase = $this->token();
        }

        $jsondata = json_decode(base64_decode($encryptedString), true);
        $salt = hex2bin($jsondata['s']);
        $ct = base64_decode($jsondata['ct']);
        $iv = hex2bin($jsondata['iv']);
        $concatedPassphrase = $passphrase.$salt;
        $md5 = [];
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];

        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
            $result .= $md5[$i];
        }

        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, $this->aesEncryptionType, $key, true, $iv);

        return json_decode($data, true);
    }

    /**
     * Get UUID
     *
     * @return string
     *------------------------------------------------------------------------ */
    public function generateUid()
    {
        try {
            // Generate a version 5 (name-based and hashed with SHA1) UUID object
            $uuid5 = Uuid::uuid4();

            return $uuid5->toString();
        } catch (UnsatisfiedDependencyException $e) {
            // Some dependency was not met. Either the method cannot be called on a
            // 32-bit system, or it can, but it relies on Moontoast\Math to be present.
            $generatedString = md5(uniqid(rand(), true));

            return implode('-', str_split($generatedString, 7));
        }
    }

    /**
     * generate token
     *
     * @return string
     *------------------------------------------------------------------------ */
    public function generateToken()
    {
        return str_replace('-', '', $this->generateUid());
    }
}
