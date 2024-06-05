<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Module\XPay\XPaymentsCloud\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CardSetup extends \XPay\XPaymentsCloud\View\CardSetup
{
    protected function getDefaultTemplate()
    {
        return count($this->getAddressList()) > 0
            ? parent::getDefaultTemplate()
            : 'modules/Qualiteam/SkinActMain/modules/XPay/XPaymentsCloud/card_setup.twig';
    }
}
