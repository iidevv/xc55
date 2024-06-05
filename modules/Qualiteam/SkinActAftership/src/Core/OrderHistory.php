<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class order history
 * @Extender\Mixin
 */
class OrderHistory extends \XLite\Core\OrderHistory
{
    public const TXT_TRACKING_INFO_ADDED_COURIER = 'SkinActAftership tracking number x is set a aftership courier x';
    public const TXT_TRACKING_INFO_CHANGED_COURIER = 'SkinActAftership tracking number x is changed aftership courier from x to x';
    public const TXT_TRACKING_ERROR = 'SkinActAftership courier slug x not found';

    public function getAftershipCouriersError($orderId, $slug)
    {
        $this->registerEvent(
            $orderId,
            static::CODE_ORDER_TRACKING,
            $this->getTrackingInfoDescription($orderId),
            $this->getTrackingInfoData($orderId),
            $this->getTrackingInfoErrorComment($slug)
        );
    }

    protected function getTrackingInfoErrorComment($slug)
    {
        return static::t(static::TXT_TRACKING_ERROR, [
            'slug' => $slug,
            'url' => \XLite\Core\Converter::buildURL('aftership_settings', '', [], \XLite::getAdminScript()),
        ]);
    }

    public function getAftershipCouriersInfoLines($added, $changed)
    {
        $comment = [];

        foreach ($added as $key => $value) {
            $comment[] = static::t(static::TXT_TRACKING_INFO_ADDED_COURIER, ['number' => $key, 'courier_name' => $value]);
        }

        foreach ($changed as $value) {
            $comment[] = static::t(static::TXT_TRACKING_INFO_CHANGED_COURIER, [
                'number' => $value['name'],
                'courier_name_old' => $value['old'],
                'courier_name_new' => $value['new'],
            ]);
        }

        return $comment;
    }
}
