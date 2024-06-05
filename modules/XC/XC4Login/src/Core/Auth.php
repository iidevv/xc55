<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\XC4Login\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Auth changes
 * @Extender\Mixin
 */
abstract class Auth extends \XLite\Core\Auth
{
    /**
     * Compare password
     *
     * @param string $hash     Hash
     * @param string $password Password string to encrypt
     *
     * @return string
     */
    public static function comparePassword($hash, $password)
    {
        $parts = explode('-', $hash, 2);

        return (count($parts) === 2 && $parts[0] === 'B')
            // we use XC4 method to compare hashes
            ? \XC\XC4Login\Main::verifyPassword($password, $hash)
            : parent::comparePassword($hash, $password);
    }
}
