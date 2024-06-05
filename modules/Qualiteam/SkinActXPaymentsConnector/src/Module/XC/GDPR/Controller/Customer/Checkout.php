<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Module\XC\GDPR\Controller\Customer;

use Includes\Utils\Module\Manager;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\AXPayments;
use XCart\Extender\Mapping\Extender;

/**
 * Checkout
 *
 * @Extender\Mixin
 * @Extender\Depend({"XC\GDPR"})
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Skips GDPR consent check to display iframe
     *
     * @return bool
     */
    protected function isIframeFLCHackRequired()
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = \XLite::getController()->getCart();

        $xpaymentsEnabled = Manager::getRegistry()->isModuleEnabled('Qualiteam\SkinActXPaymentsConnector');

        return $cart
            && $xpaymentsEnabled
            && (
                $this->getAction() === 'xpc_iframe'
                || (
                    ($this->getAction() === 'update_profile') && $this->isAJAX()
                )
            )
            && $cart->getPaymentProcessor() instanceof AXPayments;
    }
}
