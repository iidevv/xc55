<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\LanguageSelector;

use XCart\Extender\Mapping\Extender;

/**
 * Language selector (customer)
 *
 * @Extender\Mixin
 */
class Customer extends \XLite\View\LanguageSelector\Customer
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $return = parent::getJSFiles();

        $return[] = $this->getDir() . LC_DS . 'script.js';

        return $return;
    }
}
