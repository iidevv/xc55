<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\XC4Login\Core;

use XC\XC4Login\Core\Blowfish\Wrapper;

/**
 * XC4Decrypt
 */
class XC4Decrypt extends \XLite\Base\Singleton
{
    /**
     * Return decrypted text or FALSE
     *
     * @param string      $text
     * @param string|bool $key
     *
     * @return mixed
     */
    public static function textDecrypt($text, $key)
    {
        $result = false;

        $type = substr($text, 0, 1);

        if ($type === false) {
            return false;
        } elseif (substr($text, 1, 1) == '-') {
            $crc32 = true;
            $text  = substr($text, 2);
        } else {
            $crc32 = substr($text, 1, 8);
            $text  = substr($text, 9);
        }

        $result1 = trim(Wrapper::decrypt($text, $key));

        // CRC32 check
        if ($crc32 === true) {
            // Inner CRC32
            $crc32  = substr($result1, -8);
            $result = substr($result1, 0, -8);

            if (Wrapper::crc32(md5($result)) != $crc32) {
                $result = false;
            }
        } elseif ($crc32 !== false) {
            // Outer CRC32
            if (Wrapper::crc32($result1) != $crc32) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Return TRUE on correct text
     *
     * @param string $text
     * @param string $hash
     *
     * @return boolean
     */
    public static function textVerify($text, $hash)
    {
        return (new Hash\PasswordHash())->CheckPassword($text, $hash);
    }
}
