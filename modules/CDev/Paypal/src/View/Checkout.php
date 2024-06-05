<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("!CDev\SocialLogin")
 */
abstract class Checkout extends \XLite\View\Checkout
{
    /**
     * Defines the anonymous title box
     *
     * @return string
     */
    protected function getSigninAnonymousTitle()
    {
        if (!\CDev\Paypal\Core\Login::isConfigured()) {
            $result = parent::getSigninAnonymousTitle();
        } else {
            $params = [
                'text_before' => static::t('Register with'),
                'text_after'  => static::t('or go to checkout as a New customer'),
                'buttonStyle' => 'icon',
            ];

            $result = $this->getWidget($params, 'CDev\Paypal\View\Login\Widget')->getContent();
        }

        return $result;
    }
}
