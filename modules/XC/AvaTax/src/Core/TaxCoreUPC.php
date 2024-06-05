<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Core;

use XCart\Extender\Mapping\Extender;

/**
 * AcaTax client
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\SystemFields")
 */
class TaxCoreUPC extends \XC\AvaTax\Core\TaxCore
{
    /**
     * Assemble item code
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return string
     */
    protected function assembleItemCode(\XLite\Model\OrderItem $item)
    {
        $upc = $item->getProduct()->getUpcIsbn();

        return $upc ? substr('UPC:' . $upc, 0, 50) : parent::assembleItemCode($item);
    }
}
