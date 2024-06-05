<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;
use XC\Reviews\Model\OrderReviewKey;

/**
 * DataPreProcessor
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class DataPreProcessor extends \XC\ThemeTweaker\Core\Notifications\DataPreProcessor
{
    /**
     * Prepare data to pass to constructor XC\Reviews\Core\Mail\OrderReviewKey
     *
     * @param string $dir  Notification template directory
     * @param array  $data Data
     *
     * @return array
     */
    public static function prepareDataForNotification($dir, array $data)
    {
        $data = parent::prepareDataForNotification($dir, $data);

        if ($dir === 'modules/XC/Reviews/review_key') {
            $data = [
                'reviewKey' => static::getDemoOrderReviewKey($data['order'])
            ];
        }

        return $data;
    }

    /**
     * Get order review key for notification
     *
     * @param Order
     * @return OrderReviewKey
     */
    protected static function getDemoOrderReviewKey($order)
    {
        $key = null;

        if ($order) {
            $key = new OrderReviewKey();
            $key->setKeyValue('review_key');
            $key->setAddedDate(LC_START_TIME);
            $key->setSentDate(0);
            $key->setOrder($order);
        }

        return $key;
    }
}
