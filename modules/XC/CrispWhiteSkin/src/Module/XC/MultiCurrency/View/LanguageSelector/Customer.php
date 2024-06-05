<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\MultiCurrency\View\LanguageSelector;

use XCart\Extender\Mapping\Extender;

/**
 * Language selector (customer)
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiCurrency")
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
        $return[] = $this->getDir() . LC_DS . 'select.js';

        return $return;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        $return = 'modules' . LC_DS . 'XC' . LC_DS . 'MultiCurrency' . LC_DS . 'language_selector';

        return $return;
    }
}
