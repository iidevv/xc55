<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Wholesale")
 */
class SaleDiscount extends \CDev\Sale\Model\SaleDiscount
{
    /**
     * Flag: Sale is used for wholesale prices or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $applyToWholesale = false;

    /**
     * @return bool
     */
    public function getApplyToWholesale()
    {
        return $this->applyToWholesale;
    }

    /**
     * @param bool $applyToWholesale
     */
    public function setApplyToWholesale($applyToWholesale)
    {
        $this->applyToWholesale = $applyToWholesale;
    }
}
