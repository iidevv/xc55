<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\Bonuses;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\SpecialOffersBase")
 */
class BonusesSpecialOffers extends Bonuses
{
    /**
     * Get bonuses special offers url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('special_offers'),
                    'text' => static::t('SkinActYourAccountPage special offers'),
                    'is_count' => false,
                    'position' => 3
                ]
            ]
        );
    }
}