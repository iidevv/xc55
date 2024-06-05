<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\XC4Login;

abstract class Main extends \XLite\Module\AModule
{
    public static function verifyPassword($plainText, $crypted)
    {
        $decrypted = \XC\XC4Login\Core\XC4Decrypt::textDecrypt(
            $crypted,
            \XLite\Core\Config::getInstance()->XC->XC4Login->blowfish_key
        );

        return $plainText === $decrypted
            || \XC\XC4Login\Core\XC4Decrypt::textVerify(
                $plainText,
                $decrypted
            );
    }
}
