<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Menu\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated top menu widget.
 * @Extender\Mixin
 */
class Top extends \XLite\View\Menu\Customer\Top
{
    /**
     * Whether to show the Brands menu link, or not.
     *
     * @var boolean
     */
    protected $isBrandsLinkVisible;

    /**
     * Prepare items
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function prepareItems($items)
    {
        foreach ($items as $k => $item) {
            if (($item['url'] == '?target=brands') && !$this->isBrandsLinkVisible()) {
                unset($items[$k]);
                break;
            }
        }

        return $items;
    }

    /**
     * Check whether the Brands menu link should be shown, or not.
     *
     * @return bool
     */
    protected function isBrandsLinkVisible()
    {
        if (!isset($this->isBrandsLinkVisible)) {
            $this->isBrandsLinkVisible = \XLite\Core\Database::getRepo('\QSL\ShopByBrand\Model\Brand')
                    ->countEnabledBrands() > 0;
        }

        return $this->isBrandsLinkVisible;
    }
}
