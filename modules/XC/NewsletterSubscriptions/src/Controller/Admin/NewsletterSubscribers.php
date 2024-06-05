<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\Controller\Admin;

class NewsletterSubscribers extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Newsletters');
    }
}
