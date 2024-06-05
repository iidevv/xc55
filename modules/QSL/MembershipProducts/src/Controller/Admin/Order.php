<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Order page controller
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    protected function doNoAction()
    {
        parent::doNoAction();

        $order = $this->getOrder();
        if (
            $order
            && $order->getOrigProfile()
            && $order->getOrigProfile()->getAnonymous()
            && in_array(
                $order->getPaymentStatusCode(),
                \XLite\Model\Order\Status\Payment::getPaidStatuses()
            )
        ) {
            $found = false;
            foreach ($order->getItems() as $item) {
                $product = $item->getProduct();
                if ($product->getUniqueIdentifier() && $product->getAppointmentMembership()) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                \XLite\Core\TopMessage::addWarning(
                    'The order contains one or more products that give the purchaser a certain membership',
                    [
                        'url' => \XLite\Core\Converter::buildURL(
                            'profile',
                            null,
                            ['profile_id' => $order->getOrigProfile()->getProfileId()]
                        ),
                    ]
                );
            }
        }
    }
}
