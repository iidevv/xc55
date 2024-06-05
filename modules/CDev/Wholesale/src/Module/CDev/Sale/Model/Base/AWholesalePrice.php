<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\CDev\Sale\Model\Base;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @ORM\MappedSuperclass
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
abstract class AWholesalePrice extends \CDev\Wholesale\Model\Base\AWholesalePrice
{
    /**
     * Return old net product price (before sale)
     *
     * @return float
     */
    public function getNetPriceBeforeSale()
    {
        return \CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getClearPrice', ['taxable'], 'net');
    }

    /**
     * Get clear display Price
     *
     * @return float
     */
    public function getDisplayPriceBeforeSale()
    {
        return \CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getNetPriceBeforeSale', ['taxable'], 'display');
    }

    /**
     * Get clear display Price
     *
     * @return float
     */
    public function getClearDisplayPrice()
    {
        return $this->getDisplayPriceBeforeSale();
    }
}
