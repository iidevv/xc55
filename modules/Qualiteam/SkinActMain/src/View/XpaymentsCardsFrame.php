<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View;


use XPay\XPaymentsCloud\Main as XPaymentsHelper;

class XpaymentsCardsFrame extends \XLite\View\Controller
{
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActMain/XpaymentsCardsFrame.twig';
    }

    protected static function isXpaymentsEnabled()
    {
        return XPaymentsHelper::getPaymentMethod()
            && XPaymentsHelper::getPaymentMethod()->isEnabled();
    }

    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (static::isXpaymentsEnabled()) {
            $list['css'][] = 'modules/XPay/XPaymentsCloud/account/cc_type_sprites.css';
            $list['css'][] = 'modules/XPay/XPaymentsCloud/account/xpayments_cards.less';
        }

        return $list;
    }
}