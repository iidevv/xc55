<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Logic\Sitemap;

use XCart\Extender\Mapping\Extender;

/**
 * Generator
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\XMLSitemap")
 */
class Generator extends \CDev\XMLSitemap\Logic\Sitemap\Generator
{
    /**
     * Return steps list
     *
     * @return array
     */
    protected function getStepsList()
    {
        $list = parent::getStepsList();
        $list[] = 'XC\News\Logic\Sitemap\Step\News';

        return $list;
    }
}
