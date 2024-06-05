<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    protected function init($object)
    {
        parent::init($object);

        $this->prices_and_inventory->ava_tax_code = $object->getAvaTaxCode();
    }

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->setAvaTaxCode($this->prices_and_inventory->ava_tax_code);
    }
}
