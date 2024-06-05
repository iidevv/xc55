<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

class ShippingMethodRate
{
    /**
     * @param \XLite\Model\Shipping\Rate $rate
     *
     * @return array
     */
    public function mapMethodRate(\XLite\Model\Shipping\Rate $rate)
    {
        return [
            'id'   => $rate->getMethodId(),
            'shipping_name' => $rate->getMethod()->getName(),
            'details'       => $rate->getMethod()->getDeliveryTime(), // Rate has its own delivery time in extra data, should we use it as well?
            'rate'          => $rate->getTotalRate(),
        ];
    }
}
