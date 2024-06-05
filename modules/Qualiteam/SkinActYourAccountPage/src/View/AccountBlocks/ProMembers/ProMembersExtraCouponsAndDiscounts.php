<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\ProMembers;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActExtraCouponsAndDiscounts")
 */
class ProMembersExtraCouponsAndDiscounts extends ProMembers
{
    /**
     * Get pro members extra coupons and discounts url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('extra_coupons_and_discounts'),
                    'text' => static::t('SkinActYourAccountPage extra coupons and discounts'),
                    'is_count' => false,
                    'position' => 2
                ]
            ]
        );
    }
}