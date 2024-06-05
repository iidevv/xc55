<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActChangesToTrackingNumbers\View\Order;


class ListItem extends \XLite\View\Order\ListItem
{
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActChangesToTrackingNumbers/parcels_order.twig';
    }

    protected function getTrackingNumbers()
    {
        $numbers = $this->getOrder()->getTrackingNumbers();

        $result = [];
        foreach ($numbers as $number) {
            $result[] = $number->getValue();
        }

        return $result;
    }

    protected function getShippingMethod()
    {
        return $this->getOrder()->getShippingMethodName();
    }

    protected function getInstructions()
    {
        $shipping = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($this->getOrder()->getShippingId());

        if ($shipping) {
            return $shipping->getInstructions();
        }

        return '';
    }
}