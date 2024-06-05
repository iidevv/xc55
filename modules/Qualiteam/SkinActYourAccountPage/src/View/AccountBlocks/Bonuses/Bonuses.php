<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\Bonuses;

use XCart\Extender\Mapping\ListChild;
use Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourAccountBlock;

/**
 * @ListChild (list="account-blocks.element", zone="customer", weight="103")
 */
class Bonuses extends YourAccountBlock
{
    /**
     * Get bonuses title
     *
     * @return string
     */
    protected function getBlockTitle(): string
    {
        return static::t('SkinActYourAccountPage bonuses');
    }

    /**
     * Get bonuses image
     *
     * @return string
     */
    protected function getBlockImage(): string
    {
        return 'modules/Qualiteam/SkinActYourAccountPage/i-bonuses.png';
    }

    /**
     * Get bonuses url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return [];
    }
}