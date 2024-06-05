<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Sitemap\Controller\Customer;

/**
 * Map controller
 */
class Map extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Sitemap');
    }

    /**
     * Return the current page location (for the content area)
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->getTitle();
    }
}
