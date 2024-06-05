<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;
use XC\CanadaPost\Model\ProductsReturn;
use XC\CanadaPost\Model\ProductsReturn\Item as ReturnItem;

/**
 * StaticProvider
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class StaticProvider extends \XC\ThemeTweaker\Core\Notifications\StaticProvider
{
    /**
     * @inheritdoc
     */
    protected static function getNotificationsStaticData()
    {
        return parent::getNotificationsStaticData() + [
            'modules/XC/CanadaPost/return_approved' => [
                'return' => static::getProductsReturn(true),
            ],
            'modules/XC/CanadaPost/return_rejected' => [
                'return' => static::getProductsReturn(false),
            ],
        ];
    }

    /**
     * Get message object
     *
     * @return Message
     */
    protected static function getProductsReturn($approved = true)
    {
        if ($order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findDumpOrder()) {
            $return = new ProductsReturn();
            $return->setDate(time());
            $return->setStatus($approved ? ProductsReturn::STATUS_APPROVED : ProductsReturn::STATUS_REJECTED);
            $return->setNotes('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!');
            $return->setAdminNotes('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!');
            $return->setOrder($order);

            foreach ($order->getItems() as $item) {
                $rItem = new ReturnItem();
                $rItem->setReturn($return);
                $rItem->setOrderItem($item);
                $rItem->setAmount($item->getAmount());

                $return->addItems($rItem);
            }

            return $return;
        }

        return null;
    }
}
