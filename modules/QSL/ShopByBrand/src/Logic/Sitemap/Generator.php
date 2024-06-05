<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Logic\Sitemap;

use XCart\Extender\Mapping\Extender;

/**
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
        $list   = parent::getStepsList();
        $list[] = 'QSL\ShopByBrand\Logic\Sitemap\Step\BrandPage';

        return $list;
    }
}
