<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourOrders;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\VendorMessages")
 */
class YourOrdersMessages extends YourOrders
{
    /**
     * Get your orders messages url, text, is count flag and count of messages
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('messages'),
                    'text' => static::t('SkinActYourAccountPage messages'),
                    'is_count' => true,
                    'count' => $this->getMessagesCount(),
                    'position' => 3
                ]
            ]
        );
    }

    /**
     * Get count of unread messages of the user
     *
     * @return int
     */
    protected function getMessagesCount(): int
    {
        return \XLite\Core\Auth::getInstance()->isLogged()
            ? \XLite\Core\Auth::getInstance()->getProfile()->countOwnUnreadMessages()
            : 0;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();
        $params[] = \XLite\Core\Auth::getInstance()->getProfile()->countOwnUnreadMessages();

        return $params;
    }
}