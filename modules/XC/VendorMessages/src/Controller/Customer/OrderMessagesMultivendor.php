<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Order messages controller
 *
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
class OrderMessagesMultivendor extends \XC\VendorMessages\Controller\Customer\OrderMessages
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->open_dispute_popup
            ?  static::t('Open a dispute')
            : parent::getTitle();
    }

    /**
     * @inheritdoc
     */
    public function getCurrentThreadOrder()
    {
        $result = parent::getCurrentThreadOrder();

        if (\XC\VendorMessages\Main::isWarehouse() && \XC\VendorMessages\Main::isVendorAllowedToCommunicate()) {
            $found = false;
            $recipientId = intval(\XLite\Core\Request::getInstance()->recipient_id);
            if ($recipientId) {
                foreach ($result->getChildren() as $order) {
                    if ($order->getOrderId() == $recipientId) {
                        $result = $order;
                        $found = true;
                        break;
                    }
                }
            }

            if (!$found) {
                foreach ($result->getChildren() as $order) {
                    $result = $order;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Open dispute
     */
    protected function doActionOpenDispute()
    {
        \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')
            ->insert($this->createOpenDisputeMessage(), true);
        \XLite\Core\Event::orderMessagesCreate();
        \XLite\Core\TopMessage::addInfo('A dispute has been opened successfully');
    }

    /**
     * Close dispute
     */
    protected function doActionCloseDispute()
    {
        $message = $this->createCloseDisputeMessage();
        if ($message) {
            \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')
                ->insert($message, true);
            \XLite\Core\Event::orderMessagesCreate();
            \XLite\Core\TopMessage::addInfo('The dispute has been closed');
        }
    }

    /**
     * Create open dispute messages
     *
     * @return \XC\VendorMessages\Model\Message
     */
    protected function createOpenDisputeMessage()
    {
        /** @var \XC\VendorMessages\Model\Message $message */
        $message = $this->getCurrentThreadOrder()->buildNewMessage(
            \XLite\Core\Auth::getInstance()->getProfile()
        );

        if (\XLite\Core\Request::getInstance()->body) {
            $message->setBody(\XLite\Core\Request::getInstance()->body);
        }
        $message->openDispute();

        return $message;
    }

    /**
     * Create close dispute messages
     *
     * @return \XC\VendorMessages\Model\Message
     */
    protected function createCloseDisputeMessage()
    {
        /** @var \XC\VendorMessages\Model\Message $message */
        $message = $this->getCurrentThreadOrder()->buildNewMessage(
            \XLite\Core\Auth::getInstance()->getProfile()
        );

        return $message->closeDispute() ? $message : null;
    }
}
