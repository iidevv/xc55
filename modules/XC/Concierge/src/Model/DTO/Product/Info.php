<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\Product;

/**
 * Product
 * @Extender\Mixin
 */
abstract class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function afterUpdate($object, $rawData = null)
    {
        parent::afterUpdate($object, $rawData);

        Mediator::getInstance()->addMessage(
            new Product(
                'Update Product',
                $object
            )
        );
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function afterCreate($object, $rawData = null)
    {
        parent::afterCreate($object, $rawData);

        Mediator::getInstance()->addMessage(
            new Product(
                'Create Product',
                $object
            )
        );
    }
}
