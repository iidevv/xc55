<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Core\Mail;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;
use XLite\Model\Order;
use XLite\Model\Profile;
use XC\VendorMessages\Model\Message;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"XC\MultiVendor", "XC\VendorMessages"})
 */
class OrderMessageMultiVendorNotification extends \XC\VendorMessages\Core\Mail\OrderMessageNotification
{
    public function __construct(Message $message, Profile $recipient = null)
    {
        parent::__construct($message, $recipient);

        $order = $message->getConversation()->getOrder();
        $parentOrder = $order->getParent() && $order->getParent()->getOrderNumber()
            ? $order->getParent()
            : $order;

        $this->populateVariables([
            'order_number'        => $parentOrder->getOrderNumber(),
            'order_link'          => \XLite::getInstance()->getShopURL(Converter::buildURL(
                'order',
                null,
                ['order_number' => $parentOrder->getOrderNumber()],
                $recipient && !$recipient->isAdmin()
                    ? \XLite::getCustomerScript()
                    : \XLite::getAdminScript()
            )),
        ]);
    }

    /**
     * @param Order   $order
     * @param Profile $recipient
     *
     * @return string
     * @throws \Exception
     */
    protected function getOrderMessagesLink(Order $order, Profile $recipient = null)
    {
        $parentOrder = $order->getParent() && $order->getParent()->getOrderNumber()
            ? $order->getParent()
            : $order;

        if ($recipient && !$recipient->isAdmin()) {
            if ($order->getProfile()->getAnonymous()) {
                $acc = \XLite\Core\Database::getRepo('XLite\Model\AccessControlCell')->generateAccessControlCell(
                    [$order],
                    [\XLite\Model\AccessControlZoneType::ZONE_TYPE_ORDER],
                    'resendAccessLink'
                );

                $url = \XLite\Core\Converter::buildPersistentAccessURL(
                    $acc,
                    'order_messages',
                    '',
                    [
                        'order_number' => $parentOrder->getOrderNumber(),
                        'recipient_id' => $order->getOrderId(),
                    ],
                    \XLite::getCustomerScript()
                );
            } else {
                $url = \XLite\Core\Converter::buildURL(
                    'order_messages',
                    null,
                    [
                        'order_number' => $parentOrder->getOrderNumber(),
                        'recipient_id' => $order->getOrderId(),
                    ],
                    \XLite::getCustomerScript()
                );
            }
        } else {
            $url = \XLite\Core\Converter::buildURL(
                'order',
                null,
                [
                    'page'         => 'messages',
                    'order_number' => $parentOrder->getOrderNumber(),
                    'recipient_id' => $order->getOrderId(),
                ],
                \XLite::getAdminScript()
            );
        }

        return sprintf(
            '<a href="%s">%s</a>',
            htmlentities(\XLite::getInstance()->getShopURL($url)),
            $parentOrder->getOrderNumber()
        );
    }
}
