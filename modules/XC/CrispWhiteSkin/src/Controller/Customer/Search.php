<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Products search
 * @Extender\Mixin
 */
class Search extends \XLite\Controller\Customer\Search
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getMainTitle()
    {
        return null;
    }

    /**
     * Return the page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        return $this->getTitle();
    }
}
