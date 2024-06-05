<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\MembershipsAndSubscriptions;

use XCart\Extender\Mapping\ListChild;
use Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourAccountBlock;

/**
 * @ListChild (list="account-blocks.element", zone="customer", weight="105")
 */
class MembershipsAndSubscriptions extends YourAccountBlock
{
    /**
     * Get memberships and subscriptions title
     *
     * @return string
     */
    protected function getBlockTitle(): string
    {
        return static::t('SkinActYourAccountPage memberships and subscriptions');
    }

    /**
     * Get memberships and subscriptions image
     *
     * @return string
     */
    protected function getBlockImage(): string
    {
        return 'modules/Qualiteam/SkinActYourAccountPage/i-memberships.png';
    }

    /**
     * Get memberships and subscriptions url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return [];
    }
}