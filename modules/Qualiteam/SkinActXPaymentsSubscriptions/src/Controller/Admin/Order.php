<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Order page controller
 *
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Add tab for subscriptions
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        if (null !== $this->getOrder() && $this->getOrder()->hasSubscriptions()) {
            $list['x_payments_subscription'] = 'Subscriptions';
        }

        return $list;
    }

    /**
     * Add tab template for subscriptions
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if ($this->getOrder()->hasSubscriptions()) {
            $list['x_payments_subscription'] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/order/page/subscriptions.twig';
        }

        return $list;
    }
}
