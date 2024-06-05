<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\XMLSitemap\View\Admin;

use XCart\Extender\Mapping\ListChild;

/**
 * Sitemap page widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class SitemapTabs extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'sitemap';

        return $result;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/XMLSitemap/admin/tabs.twig';
    }
}
