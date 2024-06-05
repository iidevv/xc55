<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\MembershipsAndSubscriptions;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XPay\XPaymentsCloud")
 */
class MembershipsAndSubscriptionsSubscriptions extends MembershipsAndSubscriptions
{
    /**
     * Get memberships and subscriptions subscriptions url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('xpayments_subscriptions'),
                    'text' => static::t('SkinActYourAccountPage subscriptions'),
                    'is_count' => false,
                    'position' => 1
                ]
            ]
        );
    }
}