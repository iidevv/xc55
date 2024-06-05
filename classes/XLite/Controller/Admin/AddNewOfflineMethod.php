<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

class AddNewOfflineMethod extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Add offline payment method');
    }
}
