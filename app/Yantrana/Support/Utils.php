<?php

namespace App\Yantrana\Support;

use App\Yantrana\Components\Server\Repositories\AccessKeyRepository;

/**
 * Common Utils - 1.0.0 - 04 APR 2017
 *
 *
 *--------------------------------------------------------------------------- */

/**
 * This Utils class.
 *---------------------------------------------------------------- */
class Utils
{
    /**
     * Encrypt String for database
     *
     * @param  string  $payload  - Value to be encrypt
     * @return void
     *-----------------------------------------------------------------------*/
    public static function encryptForDatabase($payload)
    {
        return encrypt($payload, false);
    }

    /**
     * Decrypt String for database
     *
     * @param  string  $payload  - Value to be encrypt
     * @return void
     *-----------------------------------------------------------------------*/
    public static function decryptForDatabase($payload)
    {
        return decrypt($payload, false);
    }

    /**
     * Payload Encrypt String for database
     *
     * @param  string  $payload  - Value to be encrypt
     * @return void
     *-----------------------------------------------------------------------*/
    public static function payloadEncrypt($payload)
    {
        return urlencode(encrypt($payload));
    }

    /**
     * Payload Decrypt String for database
     *
     * @param  string  $payload  - Value to be encrypt
     * @return void
     *-----------------------------------------------------------------------*/
    public static function payloadDecrypt($payload)
    {
        return decrypt(urldecode($payload));
    }

    /**
     * Create keys files
     *
     * @param  string  $payload  - Value to be encrypt
     * @return void
     *-----------------------------------------------------------------------*/
    protected static function createKeyFiles($serverUid, $privatekey = null, $publickey = null)
    {
        if (! $privatekey) {
            $accessKeyRepository = new AccessKeyRepository();
            $accessKey = $accessKeyRepository->fetchThroughIdKey($serverUid);

            if (__isEmpty($accessKey)) {
                throw new Exception(__tr('Keys not found'), 1);
            }

            $privatekey = $accessKey->private_key;
            $publickey = $accessKey->public_key;
        }

        $serverHashUid = sha1($serverUid);

        $filePath = base_path(strtr(configItem('access_files_path'), [
            '{serverHashUid}' => $serverHashUid,
        ]));

        file_put_contents($filePath, $privatekey);
        file_put_contents($filePath.'.pub', $publickey);

        chmod($filePath, 0400);

        return $serverHashUid;
    }

    /**
     * Generate SSH Keys
     *
     * @param  string  $serverUid  - Server UID
     * @return void
     *-----------------------------------------------------------------------*/
    public static function generateAccessKeys($serverUid = null)
    {
        $rsa = new \phpseclib\Crypt\RSA();
        $instance = new static;

        $rsa->comment = configItem('access_key_comment');
        $rsa->setPublicKeyFormat(6);

        $keysCreated = $rsa->createKey(2048);

        extract($keysCreated);

        if ($serverUid) {
            $serverHashUid = $instance->createKeyFiles($serverUid, $privatekey, $publickey);
        }

        return [
            'server_id_key' => $serverUid ? $serverHashUid : null,
            'private_key' => $privatekey,
            'public_key' => $publickey,
        ];
    }

    /**
     * Remove SSH Keys files
     *
     * @param  string  $serverUid  - Server UID
     * @return void
     *-----------------------------------------------------------------------*/
    public static function removeAccessKeyFiles($serverUid = null)
    {
        if ($serverUid) {
            $serverHashUid = sha1($serverUid);

            $filePath = base_path(strtr(configItem('access_files_path'), [
                '{serverHashUid}' => $serverHashUid,
            ]));

            if (file_exists($filePath)) {
                chmod($filePath, 0600);
                unlink($filePath);
            }

            if (file_exists($filePath.'.pub')) {
                unlink($filePath.'.pub');
            }

            return true;
        }

        return false;
    }

    /**
     * Remove SSH Keys files
     *
     * @param  string  $serverUid  - Server UID
     * @return void
     *-----------------------------------------------------------------------*/
    public static function getAccessFile($serverUid = null)
    {
        if ($serverUid) {
            $serverHashUid = sha1($serverUid);

            $filePath = base_path(strtr(configItem('access_files_path'), [
                '{serverHashUid}' => $serverHashUid,
            ]));

            if (file_exists($filePath)) {
                chmod($filePath, 0400);

                return $filePath;
            } else {
                $instance = new static;
                $instance->createKeyFiles($serverUid);

                if (file_exists($filePath)) {
                    chmod($filePath, 0400);

                    return $filePath;
                }
            }
        }

        return false;
    }

    /**
     * Validate SSH key
     *
     * @param  string  $value  - Key value
     * @return bool
     *-----------------------------------------------------------------------*/
    public static function validatePublicKey($value)
    {
        $key_parts = explode(' ', $value, 3);
        if (count($key_parts) < 2) {
            return false;
        }
        if (count($key_parts) > 3) {
            return false;
        }
        $algorithm = $key_parts[0];
        $key = $key_parts[1];
        if (! in_array($algorithm, ['ssh-rsa', 'ssh-dss'])) {
            return false;
        }
        $key_base64_decoded = base64_decode($key, true);
        if ($key_base64_decoded == false) {
            return false;
        }
        $check = base64_decode(substr($key, 0, 16));
        $check = preg_replace("/[^\w\-]/", '', $check);
        if ((string) $check !== (string) $algorithm) {
            return false;
        }

        return true;
    }

    /**
     * data units conversion
     *
     * @param  int  $from  -
     * @return void
     *-----------------------------------------------------------------------*/
    public static function dataUnitsConversion($bytes)
    {
        $bytes = $bytes * (1024 * 1024);

        if ($bytes >= 1099511627776) {
            $bytes = number_format($bytes / 1099511627776).'TB';
        }
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824).'GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576).'MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024).'KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    // src: https://gist.github.com/tylerhall/521810
    //
    // Generates a strong password of N length containing at least one lower case letter,
    // one uppercase letter, one digit, and one special character. The remaining characters
    // in the password are chosen at random from those four sets.
    //
    // The available characters in each set are user friendly - there are no ambiguous
    // characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
    // makes it much easier for users to manually type or speak their passwords.
    //
    // Note: the $add_dashes option will increase the length of the password by
    // floor(sqrt(N)) characters.
    // orginal $available_sets 'luds'
    public static function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'lud')
    {
        $sets = [];
        if (strpos($available_sets, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }
        if (strpos($available_sets, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }
        if (strpos($available_sets, 'd') !== false) {
            $sets[] = '23456789';
        }
        if (strpos($available_sets, 's') !== false) {
            $sets[] = '!@#$%&*?';
        }
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }
        $password = str_shuffle($password);
        if (! $add_dashes) {
            return $password;
        }
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len).'-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;

        return $dash_str;
    }

    public static function sFtpConnection(array $serverInfo, $rootPath = '/home/servephp/.servephp/deploy-scripts', $options = [])
    {
        $instance = new static;

        $accessFilePath = $instance->getAccessFile($serverInfo['_id']);

        $sftpConfigId = 'sftp_'.$serverInfo['_id'];
        config([
            'filesystems.disks.'.$sftpConfigId => [
                'driver' => 'sftp',
                'host' => $serverInfo['ip_address'],
                'username' => array_get($options, 'username', 'servephp'),
                // 'password' => 'your-password',
                // Settings for SSH key based authentication...
                'privateKey' => $accessFilePath,
                // 'password' => 'encryption-password',
                // Optional SFTP Settings...
                // 'port' => 22,
                'root' => '/',
                // 'timeout' => 30,
            ],
        ]);

        return $sftpConfigId;
    }
}
