<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Order page controller
 *
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XLite\Controller\Admin\Order
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
    public function getPages()
    {
        $list = parent::getPages();
        if (
            isset($list['messages'])
            && !\XC\VendorMessages\Main::isVendorAllowedToCommunicate()
            && \XLite\Core\Auth::getInstance()->isVendor()
        ) {
            unset($list['messages']);
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentThreadOrder()
    {
        $result = parent::getCurrentThreadOrder();

        if (
            (\XC\VendorMessages\Main::isWarehouse()
                && \XC\VendorMessages\Main::isVendorAllowedToCommunicate()
            )
            || (!\XC\VendorMessages\Main::isWarehouse()
                && $result->isParent()
                && $result->getOrderNumber()
            )
        ) {
            if (\XLite\Core\Auth::getInstance()->isVendor()) {
                if (\XC\VendorMessages\Main::isVendorAllowedToCommunicate()) {
                    foreach ($result->getChildren() as $order) {
                        if (
                            $order->getVendor()
                            && $order->getVendor()->getProfileId() == \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()
                        ) {
                            $result = $order;
                            break;
                        }
                    }
                }
            } else {
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
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function countUnreadMessages()
    {
        return (\XLite\Core\Auth::getInstance()->isAdmin() && !\XLite\Core\Auth::getInstance()->isVendor())
            ? $this->getOrder()->countUnreadMessagesForAdmin()
            : parent::countUnreadMessages();
    }

    /**
     * Open dispute
     */
    protected function doActionOpenDispute()
    {
        if (\XLite\Core\Request::getInstance()->open_dispute_popup) {
            $this->getModelForm()->performAction('create');
            \XLite\Core\Event::orderMessagesCreate();
        } else {
            $message = $this->createOpenDisputeMessage();
            if ($message) {
                \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')
                    ->insert($message, true);
                \XLite\Core\Event::orderMessagesCreate();
                \XLite\Core\TopMessage::addInfo('A dispute has been opened successfully');
            }
            $this->restoreFormId();
        }
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
        $this->restoreFormId();
    }

    /**
     * @inheritdoc
     */
    protected function getModelFormClass()
    {
        return $this->getAction() == 'open_dispute' && \XLite\Core\Request::getInstance()->open_dispute_popup
            ? '\XC\VendorMessages\View\Model\MessageDispute'
            : parent::getModelFormClass();
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

        return $message->openDispute() ? $message : null;
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
        $message->closeDispute();

        return $message;
    }
}
