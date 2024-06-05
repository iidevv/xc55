<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\ItemsList\Model\Payment\Item;

use Qualiteam\SkinActXPaymentsConnector\Core\Settings;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Layout;

/**
 * @Extender\Mixin
 */
class PaymentMethod extends \XLite\View\ItemsList\Model\Payment\Item\PaymentMethod
{
    /**
     * @return string
     */
    public function getAdminIconURL()
    {
        $method = $this->getPayment();

        if (Settings::XP_MODULE_NAME === $method->getModuleName()) {
            $url = $method->getAdminIconURL();

            if (
                !$url
                && $method->isModuleInstalled()
                && !$method->isModuleEnabled()
            ) {
                $url = Layout::getInstance()
                    ->getResourceWebPath('modules/Qualiteam/SkinActXPaymentsConnector/method_icon_xp.png');
            }

        } else {
            $url = parent::getAdminIconURL();
        }

        return $url;
    }
}
