<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActNewCheckout\Model\Payment\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class XPaymentsCloud extends \XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud
{
    public function getCheckoutTemplate(\XLite\Model\Payment\Method $method)
    {
        return 'modules/XPay/XPaymentsCloud/checkout/widget.twig';
    }

    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        if (isset($_ENV['XP_ALWAYS_ENABLED']) && $_ENV['XP_ALWAYS_ENABLED'] === '1') {
            return true;
        }

        return parent::isConfigured($method);
    }

    public function getInputTemplate()
    {
        return null;
    }
}
