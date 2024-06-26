<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\Lock;

/**
 * Marketplace locker
 */
class PaypalCommerceOnboardingLocker extends \XLite\Core\Lock\AObjectCacheLocker
{
    /**
     * Default TTL to wait until lock released (in seconds)
     *
     * @var integer
     */
    public const TTL_WAIT = 10;

    /**
     * Get object identifier
     *
     * @param string $object Object identifier (action name)
     *
     * @return string
     */
    protected function getIdentifier($object)
    {
        return $object;
    }
}
