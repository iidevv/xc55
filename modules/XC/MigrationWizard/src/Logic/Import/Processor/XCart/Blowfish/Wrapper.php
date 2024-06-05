<?php
// phpcs:ignoreFile
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Blowfish;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Blowfish\Base;

/**
 * Wrapper
 */
class Wrapper
{
    private static $blowfish = null;

    private static function func_get_blowfish()
    {
        if (self::$blowfish === null) {
            self::$blowfish = new Base();
        }

        return self::$blowfish;
    }

    /**
     * Checking if wrappers need to be used
     */
    private static function bf_check_env()
    {
        if (empty(self::$blowfish) || !empty(Base::$BF_MODE)) {
            return;
        }

        // Mcrypt check
        if (empty(Base::$BF_MODE)) {
            $data = '098f6bcd4621d373cade4e832627b4f6';
            $key = '283be88071814577787744fa58f06d8f';
            $base = new Base();

            $encrypted_data = $base->mcrypt($data, $key);
            $decrypted_data = $base->mdecrypt($encrypted_data, $key);

            if ($data === $decrypted_data) {
                Base::$BF_MODE = 1;
            }
        }

        // Wrappers check
        if (empty(Base::$BF_MODE)) {
            $s = 'test';
            $key = '8d5db63ada15e11643a0b1c3477c2c5c';
            self::$blowfish->setDumpSymbol(" ");
            $s = self::$blowfish->expand($s);

            Base::$BF_MODE = (self::$blowfish->ctEncrypt($s, $key) == 'c1b2e0cda54a428e')
                    ? 2 : 3;
        }
    }

    private static function func_int_pack($x)
    {
        if ($x < 0) {
            $x += 4294967296;
        }

        for ($i = 0; $i < 4; $i++) {
            if ($x >= 256) {
                $f = floor($x / 256);
                $p[$i] = chr($x - $f * 256);
                $x = $f;
            } else {
                $p[$i] = chr($x);
                $x = 0;
            }
        }

        return $p;
    }

    /**
     * Crypt Blowfish wrapper
     */
    public static function crypt($s, $key)
    {
        if (!self::func_get_blowfish()) {
            return false;
        }

        self::bf_check_env();
        self::$blowfish->setDumpSymbol(" ");
        $s = self::$blowfish->expand($s);

        return self::$blowfish->ctEncrypt($s, $key);
    }

    /**
     * Decrypt Blowfish wrapper
     */
    public static function decrypt($s, $key)
    {
        if (!self::func_get_blowfish()) {
            return false;
        }

        self::bf_check_env();
        return self::$blowfish->ctDecrypt($s, $key);
    }

    /**
     * Get CRC32 as HEX representation of integer
     */
    public static function crc32($str)
    {
        $crc32 = crc32($str);

        if (crc32('test') != -662733300 && $crc32 > 2147483647) {
            $crc32 -= 4294967296;
        }

        $hex = dechex(abs($crc32));

        return str_repeat('0', 8 - strlen($hex)) . $hex;
    }

    public static function func_xor($a, $b)
    {
        if (
            empty(Base::$BF_MODE) || Base::$BF_MODE == 2 || ($a < 2147483648 && $b < 2147483648
            && $a > -2147483648 && $b > -2147483648)
        ) {
            return $a ^ $b;
        }

        $p1 = self::func_int_pack($a);
        $p2 = self::func_int_pack($b);

        $arr = unpack('V', ($p1[0] ^ $p2[0]) . ($p1[1] ^ $p2[1]) . ($p1[2] ^ $p2[2]) . ($p1[3]
            ^ $p2[3]));
        return array_pop($arr);
    }

    public static function func_and($a, $b)
    {
        if (
            empty(Base::$BF_MODE) || Base::$BF_MODE == 2 || ($a < 2147483648 && $b < 2147483648
            && $a > -2147483648 && $b > -2147483648)
        ) {
            return $a & $b;
        }

        $p1 = self::func_int_pack($a);
        $p2 = self::func_int_pack($b);

        $arr = unpack('V', ($p1[0] & $p2[0]) . ($p1[1] & $p2[1]) . ($p1[2] & $p2[2]) . ($p1[3]
            & $p2[3]));
        return array_pop($arr);
    }

    public static function func_rshift($a, $b)
    {
        if (
            empty(Base::$BF_MODE) || Base::$BF_MODE == 2 || ($a < 2147483648 && $b < 2147483648
            && $a > -2147483648 && $b > -2147483648)
        ) {
            return $a >> $b;
        }

        if ($b > 32) {
            $b = $b % 32;
        } elseif ($b < 0) {
            $b = 32 + ($b % 32);
        }

        if ($a > 0) {
            return intval(floor($a / pow(2, $b)) - pow(2, 32 - $b));
        } else {
            return intval(floor($a / pow(2, $b)) + pow(2, 32 - $b));
        }
    }

    /**
     * Convert string to hex
     */
    public static function func_str2hex($str)
    {
        $ret = '';
        $l = strlen($str);
        for ($i = 0; $i < $l; $i++) {
            $r = dechex(ord(substr($str, $i, 1)));
            if (strlen($r) == 1) {
                $r = '0' . $r;
            }
            $ret .= $r;
        }

        return $ret;
    }

    /**
     * Convert hex to string
     */
    public static function func_hex2str($str)
    {
        // https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.invalid-base-characters
        // hexdec() will ignore any non-hexadecimal characters it encounters. As of PHP 7.4.0 supplying any invalid characters is deprecated.
        $ret = '';
        $l = strlen($str);
        for ($i = 0; $i < $l; $i += 2) {
            $ret .= @chr(@hexdec(substr($str, $i, 2)));
        }

        return $ret;
    }
}
