<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\CDev\Sale\View\ItemsList;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
class WholesalePrices extends \CDev\Wholesale\View\ItemsList\WholesalePrices
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->isOnAbsoluteSale();
    }

    /**
     * @return bool
     */
    protected function isOnAbsoluteSale()
    {
        return $this->getProduct()->getParticipateSale()
            && $this->getProduct()->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE;
    }
}
