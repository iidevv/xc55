<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core\TokenReplacer;

/**
 * Provides methods to replace tokens with store information.
 */
class StoreInformation extends ATokenReplacer
{
    /**
     * Return list of allowed tokens.
     *
     * @return array
     */
    public static function getAllowedTokens()
    {
        return [
            'COMPANY_NAME',
        ];
    }

    /**
     * Return replacement string for the NAME token.
     *
     * @return string
     */
    protected function getTokenStringCompanyName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }
}
