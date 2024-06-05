<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\LoginAndSecurity;

use XCart\Extender\Mapping\ListChild;
use Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourAccountBlock;

/**
 * @ListChild (list="account-blocks.element", zone="customer", weight="102")
 */
class LoginAndSecurity extends YourAccountBlock
{
    /**
     * Get login and security title
     *
     * @return string
     */
    protected function getBlockTitle(): string
    {
        return static::t('SkinActYourAccountPage login and security');
    }

    /**
     * Get login and security image
     *
     * @return string
     */
    protected function getBlockImage(): string
    {
        return 'modules/Qualiteam/SkinActYourAccountPage/i-login.png';
    }

    /**
     * Get login and security url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return [
            [
                'url' => $this->buildURL('profile'),
                'text' => static::t('SkinActYourAccountPage account details'),
                'is_count' => false,
                'position' => 1
            ],
            [
                'url' => $this->buildURL('address_book'),
                'text' => static::t('SkinActYourAccountPage your addresses'),
                'is_count' => false,
                'position' => 2
            ],
            [
                'url' => $this->buildURL('login', 'logoff'),
                'text' => static::t('SkinActYourAccountPage sign out'),
                'is_count' => false,
                'position' => 3
            ]
        ];
    }
}