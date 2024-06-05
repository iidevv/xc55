<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Order history main point of execution
 * @Extender\Mixin
 */
class OrderHistory extends \XLite\Core\OrderHistory
{
    public const CODE_MEMBERSHIP_PRODUCTS_CHANGE = 'CHANGE_MEMBERSHIP_PRODUCTS';
    public const TXT_MEMBERSHIP_PRODUCTS_CHANGE  = 'Customer membership level changed';

    /**
     * Register membership product changes
     *
     * @param integer $orderId Order id
     * @param array   $change  Structure
     */
    public function registerOrderMembershipProductChange($orderId, $change)
    {
        $this->registerEvent(
            $orderId,
            static::CODE_MEMBERSHIP_PRODUCTS_CHANGE,
            static::TXT_MEMBERSHIP_PRODUCTS_CHANGE,
            [],
            '',
            [
                [
                    'name'  => (string) 'From membership',
                    'value' => (string) static::t($change['oldMembership']),
                ],
                [
                    'name'  => (string) 'To membership',
                    'value' => (string) static::t($change['newMembership']),
                ],
            ]
        );
    }
}
