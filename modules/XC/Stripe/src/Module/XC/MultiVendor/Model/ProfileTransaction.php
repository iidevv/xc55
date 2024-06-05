<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Module\XC\MultiVendor\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class ProfileTransaction extends \XC\MultiVendor\Model\ProfileTransaction
{
    public const PROVIDER_STRIPE_CONNECT = 'SC';

    /**
     * Get provider image url
     *
     * @return string
     */
    public function getProviderImageUrl()
    {
        if ($this->getProvider() === static::PROVIDER_STRIPE_CONNECT) {
            return \XLite\Core\Layout::getInstance()->getResourceWebPath('modules/XC/Stripe/method_icon.png');
        }

        return parent::getProviderImageUrl();
    }
}
