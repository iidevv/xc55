<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Controller\Customer;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 * Checkout
 *
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Set shipping addresses for subscriptions at the end of checkout in case of success
     *
     * @return void
     */
    public function processSucceed($fullProcess = true)
    {
        parent::processSucceed($fullProcess);

        $cart = $this->getCart();
        $needFlush = false;

        $shippingAddress = $cart->getProfile()->getShippingAddress();
        $shippingId = $cart->getShippingId();

        foreach ($cart->getItems() as $orderItem) {
            $subscription = $orderItem->getSubscription();
            if ($subscription) {
                $subscription->setShippingAddress($shippingAddress);
                $subscription->setShippingId($shippingId);

                if (isset(Request::getInstance()->payment['saved_card_id'])) {
                    // Save original saved card id for subscription (instead of cloned)
                    $card = Database::getRepo(XpcTransactionData::class)
                        ->find(Request::getInstance()->payment['saved_card_id']);
                    if ($card) {
                        $cart->setSavedCardForSubscriptions($card);
                    }
                }

                $needFlush = true;
            }
        }

        if ($needFlush) {
            Database::getEM()->flush();
        }
    }
}
