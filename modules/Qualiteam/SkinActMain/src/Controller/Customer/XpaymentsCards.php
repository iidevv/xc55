<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActMain\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use \XLite\Core\Session;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class XpaymentsCards extends \XPay\XPaymentsCloud\Controller\Customer\XpaymentsCards
{
    protected function reloadPage($url = null)
    {
        if (Request::getInstance()->XpaymentsCardsFrame && !str_contains($url, 'checkoutPayment')) {
            $url = $this->buildURL('xpayments_cards_frame', '', ['token' => Session::getInstance()->cartToken]);
        }

        parent::reloadPage($url);
    }
}