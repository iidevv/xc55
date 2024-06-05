<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Product Search form.
 * @Extender\Mixin
 */
class Search extends \XLite\View\Search
{
    /**
     * Whether the brand search option is visible, or not.
     *
     * @var boolean
     */
    protected $isBrandFieldVisible;

    /**
     * Check if the "Search by brand" option should be displayed in the form.
     *
     * @return bool
     */
    public function isBrandFieldVisible()
    {
        if (!isset($this->isBrandFieldVisible)) {
            $this->isBrandFieldVisible = \XLite\Core\Database::getRepo('\QSL\ShopByBrand\Model\Brand')
                    ->countEnabledBrands() > 0;
        }

        return $this->isBrandFieldVisible;
    }
}
