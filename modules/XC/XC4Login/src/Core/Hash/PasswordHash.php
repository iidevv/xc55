<?php
// phpcs:ignoreFile
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\XC4Login\Core\Hash;

use XC\XC4Login\Core\Hash\Base;

/**
 * Password hash
 */
class PasswordHash extends Base
{

    public const ITERATION_COUNT_LOG2 = 11;
    public const USE_STRONG_HASH = false;
    public const HASH_PREFIX = '$XCHash$';

    public function __construct()
    {
        parent::__construct(self::ITERATION_COUNT_LOG2, self::USE_STRONG_HASH);
    }

    public function get_random_bytes($count)
    {
        $output = '';

        if (function_exists('openssl_random_pseudo_bytes')) {
            $output = openssl_random_pseudo_bytes($count, $crypto_strong);
            if (!$crypto_strong) {
                $output = '';
            }
        }

        if (
            strlen($output) != $count && @is_readable('/dev/urandom') && ($fh = @fopen('/dev/urandom', 'rb'))
        ) {
            $output = fread($fh, $count);
            fclose($fh);
        }

        if (strlen($output) != $count) {
            $output = parent::get_random_bytes($count);
        }

        return $output;
    }

    public function HashPassword($password)
    {
        $return = parent::HashPassword($password);

        if ($return[0] != '*') {
            $return = self::HASH_PREFIX . $return;
        }

        return $return;
    }

    public function CheckPassword($password, $stored_hash)
    {
        if (self::isPasswordHash($stored_hash)) {
            $stored_hash = substr($stored_hash, strlen(self::HASH_PREFIX));
        }

        return parent::CheckPassword($password, $stored_hash);
    }

    public static function isPasswordHash($check_hash)
    {
        return (strpos($check_hash, self::HASH_PREFIX) === 0);
    }
}
