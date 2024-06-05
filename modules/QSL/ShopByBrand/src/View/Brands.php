<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

/**
 * Brands page view
 */
class Brands extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['brands']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ShopByBrand/brands/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return bool
     */
    protected function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->count();
    }
}
