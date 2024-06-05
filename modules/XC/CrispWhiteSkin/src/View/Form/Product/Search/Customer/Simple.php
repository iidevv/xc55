<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Form\Product\Search\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Simple form for searching products widget
 * @Extender\Mixin
 */
abstract class Simple extends \XLite\View\Form\Product\Search\Customer\Simple
{
    /**
     * Returns id attribute value for substring input field
     *
     * @return string
     */
    protected function getSearchSubstringInputId()
    {
        return $this->getUniqueId(parent::getSearchSubstringInputId());
    }
}
