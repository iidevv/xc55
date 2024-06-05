<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\Bonuses;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\LoyaltyProgram")
 */
class BonusesBonuses extends Bonuses
{
    /**
     * Get bonuses bonuses url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('reward_points'),
                    'text' => static::t('SkinActYourAccountPage bonuses'),
                    'is_count' => false,
                    'position' => 2
                ]
            ]
        );
    }
}