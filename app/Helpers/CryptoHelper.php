<?php

namespace App\Helpers;

class CryptoHelper {

    public static function encryptPassword($password)
        {
            $secretKey = base64_decode(config('app.aes_key'));
            $iv = config('app.aes_iv');

            if (!$secretKey || strlen($iv) !== 16) {
                return 'Error: Invalid secret key or IV length';
            }

            $encrypted = openssl_encrypt(
                $password,
                'AES-256-CBC',
                $secretKey,
                OPENSSL_RAW_DATA,
                $iv
            );

            return $encrypted ? base64_encode($encrypted) : 'Error: Encryption failed';
        }
    public static function decryptPassword($encryptedPassword) {

        $secretKey = base64_decode(config('app.aes_key'));
        $iv = config('app.aes_iv');

        if (!$secretKey || strlen($iv) !== 16) {
            return 'Error: Invalid secret key or IV length';
        }

        $encryptedPassword = urldecode($encryptedPassword);

        $encryptedPassword = str_replace(' ', '+', $encryptedPassword);

        // ✅ Base64 decode
        $encryptedPassword = base64_decode($encryptedPassword);

        if (!$encryptedPassword) {
            return 'Error: Invalid Base64 encoding';
        }

        $decrypted = openssl_decrypt(
            $encryptedPassword,
            'AES-256-CBC',
            $secretKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted ?: 'Error: Decryption failed';
    }

    public static function encryptAES256GCM(string $plaintext, string $base64Key): string
    {
        $key = base64_decode(trim($base64Key), true);  // returns 32 bytes
        // var_dump(strlen($key)); // 32 âœ…

        if (!$key || strlen($key) !== 32) {
            // throw new RuntimeException('Invalid AES key');
            return 'invalid key';
        }

        $iv = random_bytes(12);  // 96-bit nonce
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Encryption failed');
        }

        // Store as: base64( IV || TAG || CIPHERTEXT )
        return base64_encode($iv . $tag . $ciphertext);
    }

    public static function decryptAES256GCM(string $encrypted, string $base64Key): string
    {
        $key = base64_decode(trim($base64Key), true);  // returns 32 bytes

        if (!$key || strlen($key) !== 32) {
            return false;
        }

        $data = base64_decode($encrypted, true);
        if ($data === false || strlen($data) < 28) {
            return false;
        }

        $iv = substr($data, 0, 12);
        $tag = substr($data, 12, 16);
        $ciphertext = substr($data, 28);

        return openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
    }
    
}
