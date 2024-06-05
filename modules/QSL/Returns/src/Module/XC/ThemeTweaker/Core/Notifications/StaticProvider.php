<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use QSL\Returns\Model\OrderReturn;
use QSL\Returns\Model\ReturnAction;
use QSL\Returns\Model\ReturnItem;
use QSL\Returns\Model\ReturnReason;

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
            'modules/QSL/Returns/return/created' => [
                'order' => static::getOrderForPreview(OrderReturn::STATUS_ISSUED)
            ],
            'modules/QSL/Returns/return/completed' => [
                'order' => static::getOrderForPreview(OrderReturn::STATUS_COMPLETED)
            ],
            'modules/QSL/Returns/return/declined' => [
                'order' => static::getOrderForPreview(OrderReturn::STATUS_DECLINED)
            ],
        ];
    }

    /**
     * Get order object
     *
     * @return \XLite\Model\Order
     */
    protected static function getOrderForPreview($status)
    {
        /** @var \XLite\Model\Order $order */
        if ($order = Database::getRepo('XLite\Model\Order')->findDumpOrder()) {
            $order = $order->cloneOrderAsTemporary();

            $return = $order->getOrderReturn();
            if (! $return) {
                /** @var \XLite\Model\OrderItem $orderItem */
                $orderItem = $order->getItems()->first();

                if ($orderItem) {
                    /** @var ReturnItem $returnItem */
                    $returnItem = new ReturnItem();
                    $returnItem->setAmount(1);
                    $returnItem->setOrderItem($orderItem);

                    /** @var OrderReturn $return */
                    $return = new OrderReturn();
                    $return->setComment('Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ab architecto aut commodi consequatur delectus distinctio earum excepturi iusto laboriosam quaerat recusandae, repellendus ut, veritatis vitae? Ipsum iste nostrum saepe!');
                    $return->setDate(new \DateTime('-1 day'));
                    $return->setStatus($status);
                    $return->setReason(Database::getRepo(ReturnReason::class)->findOneBy([]));
                    $return->setAction(Database::getRepo(ReturnAction::class)->findOneBy([]));
                    $return->getItems()->add($returnItem);

                    $order->setOrderReturn($return);
                }
            }

            return $order;
        }

        return null;
    }
}
