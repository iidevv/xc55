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
class MembershipsAndSubscriptionsSavedCreditCards extends MembershipsAndSubscriptions
{
    /**
     * Get memberships and subscriptions saved credit cards url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('xpayments_cards'),
                    'text' => static::t('SkinActYourAccountPage saved credit cards'),
                    'is_count' => false,
                    'position' => 2
                ]
            ]
        );
    }
}