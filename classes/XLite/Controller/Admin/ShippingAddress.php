<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

class ShippingAddress extends \XLite\Controller\Admin\Settings
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Shipping');
    }

    /**
     * Get options for current tab (category)
     *
     * @param bool $getAllOptions
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions($getAllOptions = false)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')->findAddressOptions();
    }
}
