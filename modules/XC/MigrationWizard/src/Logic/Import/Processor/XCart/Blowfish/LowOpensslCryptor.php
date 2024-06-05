<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Blowfish;

/**
 * Blowfish Via Openssl
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 */

// CRypt/DEcrypt via OPenssl
class LowOpensslCryptor
{
    private $cipher_algo;
    private $hash_algo;
    private $iv_num_bytes;
    private $format;

    public const FORMAT_RAW = 0;
    public const FORMAT_B64 = 1;
    public const FORMAT_HEX = 2;
    public const DEF_CIPHER_ALGO = 'aes-256-ctr';
    public const DEF_HASH_ALGO = 'sha256';

    public const VECTOR_DELIM_DATA = ':::';

    /**
     * Construct a LOWOpensslCryptor, using aes256 encryption, sha256 key hashing and base64 encoding.
     * @param string $cipher_algo The cipher algorithm.
     * @param string $hash_algo   Key hashing algorithm.
     * @param [type] $fmt         Format of the encrypted data.
     */
    public function __construct($cipher_algo = self::DEF_CIPHER_ALGO, $hash_algo = self::DEF_HASH_ALGO, $fmt = self::FORMAT_B64)
    {
        $this->cipher_algo = $cipher_algo;
        $this->hash_algo = $hash_algo;
        $this->format = $fmt;

        $this->iv_num_bytes = openssl_cipher_iv_length($cipher_algo);
    }


    /**
     * Decrypt a string.
     * @param  string $in  String to DEcrypt.
     * @param  string $key Decryption key.
     * @param  int $fmt Optional override for the input encoding. One of FORMAT_RAW, FORMAT_B64 or FORMAT_HEX.
     * @return string      The DEcrypted string.
     */
    public function decryptString($in, $key, $fmt = null)
    {
        if ($fmt === null) {
            $fmt = $this->format;
        }

        $raw = $in;

        // Restore the encrypted data if encoded
        if ($fmt == static::FORMAT_B64) {
            $raw = base64_decode($in);
        } elseif ($fmt == static::FORMAT_HEX) {
            $raw = pack('H*', $in);
        }

        if (strpos($raw, static::VECTOR_DELIM_DATA) === false) {
            throw new \Exception(__METHOD__ . ' - Not an openssl cipher');
        } elseif (strlen($raw . static::VECTOR_DELIM_DATA) < $this->iv_num_bytes) {
            // and do an integrity check on the size.
            throw new \Exception(__METHOD__ . ' - data length ' . strlen($raw) . " is less than iv length {$this->iv_num_bytes}");
        }

        // Extract the initialisation vector and encrypted data
        [$iv, $raw] = explode(static::VECTOR_DELIM_DATA, $raw, 2);

        // Hash the key
        $keyhash = openssl_digest($key, $this->hash_algo, true);
        if ($keyhash === false) {
            throw new \Exception(__METHOD__ . ' digest failed: ' . openssl_error_string());
        }

        // and DEcrypt.
        $opts = OPENSSL_RAW_DATA;
        $res = openssl_decrypt($raw, $this->cipher_algo, $keyhash, $opts, base64_decode($iv));

        if ($res === false) {
            throw new \Exception(__METHOD__ . ' decryption failed: ' . openssl_error_string());
        }

        return $res;
    }

    /**
     * Static convenience method for ENcrypting.
     * @param  string $in  String to ENcrypt.
     * @param  string $key ENcryption key.
     * @param  int $fmt Optional override for the output encoding. One of FORMAT_RAW, FORMAT_B64 or FORMAT_HEX.
     * @return string      The encrypted string.
     */
}
