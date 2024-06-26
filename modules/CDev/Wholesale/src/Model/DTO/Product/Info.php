<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);
        $this->prices_and_inventory->applySaleToWholesale = $object->getApplySaleToWholesale();
    }

    /**
     * @inheritdoc
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);
        $object->setApplySaleToWholesale((string)$this->prices_and_inventory->applySaleToWholesale);
    }
}
