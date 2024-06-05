<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\ProMembers;

use Qualiteam\SkinActProMembership\Helpers\Profile;
use XCart\Extender\Mapping\ListChild;
use Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourAccountBlock;

/**
 * @ListChild (list="account-blocks.element", zone="customer", weight="104")
 * @Extender\Depend("Qualiteam\SkinActProMembership")
 */
class ProMembers extends YourAccountBlock
{
    /**
     * Get pro members title
     *
     * @return string
     */
    protected function getBlockTitle(): string
    {
        return static::t('SkinActYourAccountPage pro members');
    }

    /**
     * Get pro members image
     *
     * @return string
     */
    protected function getBlockImage(): string
    {
        return 'modules/Qualiteam/SkinActYourAccountPage/i-promembers.png';
    }

    /**
     * Get pro members url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return [];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible(): bool
    {
        return parent::isVisible() && (new Profile)->isProfileProMembership();
    }
}