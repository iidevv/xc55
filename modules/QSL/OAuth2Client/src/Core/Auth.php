<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Authorization routine
 * @Extender\Mixin
 */
class Auth extends \XLite\Core\Auth
{
    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return boolean
     */
    public function loginOAuth2Profile(\QSL\OAuth2Client\Model\ExternalProfile $profile)
    {
        $result = false;

        // Initialize order Id
        $orderId = \XLite\Core\Request::getInstance()->anonymous
            ? \XLite\Model\Cart::getInstance()->getOrderId()
            : 0;

        if (\XLite\Core\Auth::getInstance()->loginProfile($profile->getProfile())) {
            $profile->setLastLoginDate(\XLite\Core\Converter::time());

            // Renew order
            $orderId = $orderId ?: \XLite\Core\Session::getInstance()->order_id;
            /** @var \XLite\Model\Cart $order */ #nolint
            $order = \XLite\Core\Database::getRepo('XLite\Model\Cart')->find($orderId);
            if ($order) {
                $order->login($profile->getProfile());
                $order->renew();
            }

            $result = true;
        }

        return $result;
    }
}
