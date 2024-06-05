<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product
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

        $this->initFreeShipping($object);
    }

    protected function initFreeShipping($object)
    {
        static::compose(
            $this,
            [
                'shipping' => [
                    'requires_shipping' => [
                        'ship_for_free'          => $object->isShipForFree(),
                        'free_shipping'          => $object->getFreeShip(),
                    ],
                ],
            ]
        );

        $this->shipping->fixed_shipping_freight = $object->getFreightFixedFee();
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return void
     */
    public function populateTo($object, $rawData = null)
    {
        $this->populateToFreeShipping($object);

        parent::populateTo($object, $rawData);
    }

    protected function populateToFreeShipping($object)
    {
        $shipForFree = static::deCompose($this, 'shipping', 'requires_shipping', 'ship_for_free');
        $object->setShipForFree((bool)$shipForFree);

        $freeShipping = static::deCompose($this, 'shipping', 'requires_shipping', 'free_shipping');
        $object->setFreeShip((bool) $freeShipping);

        $fixedShippingFreight = $this->shipping->fixed_shipping_freight;
        $object->setFreightFixedFee((float) $fixedShippingFreight);
    }
}
