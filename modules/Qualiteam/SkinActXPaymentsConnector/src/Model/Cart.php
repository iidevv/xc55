<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Model;

use Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\SavedCard;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;
use XCart\Extender\Mapping\Extender;

/**
 * XPayments payment processor
 *
 * @Extender\Mixin
 */
class Cart extends \XLite\Model\Cart
{
    /**
     * Checks if any X-Payments payment methods are available for this cart
     *
     * @return boolean
     */
    public function isXpcMethodsAvailable()
    {
        $found = false;
        foreach ($this->getPaymentMethods() as $method) {
            if (
                XPayments::class == $method->getClass()
                || SavedCard::class == $method->getClass()
            ) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    /**
     * If we can proceed with checkout with current cart
     *
     * @return boolean
     */
    public function checkCart()
    {
        $result = parent::checkCart();

        if (
            XPaymentsClient::getInstance()->isModuleConfigured()
            && !$result
        ) {
            XPaymentsClient::getInstance()->clearInitDataFromSession();
        }

        return $result;
    }
}
