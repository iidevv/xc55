<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * API Settings
 */
class APISettings extends \XLite\Controller\Admin\Settings
{
    /**
     * Page
     *
     * @var string
     */
    public $page = self::API_PAGE;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('API Settings');
    }
}
