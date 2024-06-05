<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\View;

use XCart\Extender\Mapping\ListChild;

/**
 * News messages page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class NewsMessages extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['news_messages']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/News/news_messages/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return boolean
     */
    protected function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')->count();
    }
}
