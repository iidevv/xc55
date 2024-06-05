<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\ItemsList\Messages\Base;

use XCart\Extender\Mapping\Extender;

/**
 * Order messages
 *
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
abstract class OrderMultivendor extends \XC\VendorMessages\View\ItemsList\Messages\Base\Order
{
    /**
     * Author types
     */
    public const AUTHOR_TYPE_VENDOR   = 'vendor';
    /**
     * @inheritdoc
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/xcart.popup.js';
        $list[static::RESOURCE_JS][] = 'js/xcart.popup_button.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/VendorMessages/button/open_dispute.js';
        $list[] = 'modules/XC/VendorMessages/popup/dispute.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function isAllowDispute()
    {
        return \XC\VendorMessages\Main::isAllowDisputes()
            && !\XLite\Core\Auth::getInstance()->isVendor();
    }

    /**
     * @inheritdoc
     */
    protected function isOpenedDispute()
    {
        return $this->getCurrentThreadOrder()->getIsOpenedDispute();
    }

    /**
     * @inheritdoc
     */
    protected function isRecipientSelectorVisible()
    {
        return \XC\VendorMessages\Main::isWarehouse()
            && \XC\VendorMessages\Main::isVendorAllowedToCommunicate()
            && !\XLite\Core\Auth::getInstance()->isVendor()
            && count($this->getOrder()->getChildren()) > 1;
    }

    /**
     * @inheritdoc
     */
    protected function getRecipients()
    {
        $result = parent::getRecipients();
        foreach ($this->getOrder()->getChildren() as $order) {
            if ($order->getVendor()) {
                $result[$order->getOrderId()] = $order->getVendor()->getNameForMessages();
            } else {
                $result[$order->getOrderId()] = $result[0];
            }
        }

        if (
            \XC\VendorMessages\Main::isWarehouse()
            && \XC\VendorMessages\Main::isVendorAllowedToCommunicate()
            && isset($result[0])
        ) {
            unset($result[0]);
        }

        return $result;
    }

    /**
     * Get tabs
     *
     * @return array[]
     */
    protected function getTabs()
    {
        $tabs = [];

        $found = false;
        foreach ($this->getRecipients() as $rid => $recipient) {
            $order = $this->getOrder();
            if ($rid != 0 && $rid != $this->getOrder()->getOrderId()) {
                foreach ($this->getOrder()->getChildren() as $o) {
                    if ($o->getOrderId() == $rid) {
                        $order = $o;
                        break;
                    }
                }
            }

            $tab = [
                'selected'    => $rid == \XLite\Core\Request::getInstance()->recipient_id,
                'url'         => \XLite::isAdminZone()
                    ? static::buildURL('order', null, ['order_number' => $this->getOrder()->getOrderNumber(), 'page' => 'messages', 'recipient_id' => $rid])
                    : static::buildURL('order_messages', null, ['order_number' => $this->getOrder()->getOrderNumber(), 'recipient_id' => $rid]),
                'title'       => $recipient,
                'countUnread' => (\XLite\Core\Auth::getInstance()->isAdmin() && !\XLite\Core\Auth::getInstance()->isVendor())
                    ? $order->countUnreadMessagesForAdmin()
                    : $order->countOwnUnreadMessages(),
                'has_dispute' => $order->getIsOpenedDispute(),
            ];
            $tab['marks_visible'] =  $tab['countUnread'] || $tab['has_dispute'];
            if (!$found && $tab['selected']) {
                $found = true;
            }

            $tabs[] = $tab;
        }

        if (!$found && $tabs) {
            $tabs[0]['selected'] = true;
        }

        return $tabs;
    }

    /**
     * Get arguments for dispute label
     *
     * @return string[]
     */
    protected function getDisputeMessageArguments()
    {
        $message = \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')
            ->findOneLastOpenDispute($this->getCurrentThreadOrder());

        return [
            'date' => $message ? $this->formatDate($message->getDate()) : static::t('n/a'),
            'name' => $message ? $message->getAuthorName() : static::t('n/a'),
        ];
    }

    /**
     * Get author type
     *
     * @param \XC\VendorMessages\Model\Message $message
     *
     * @return string
     */
    protected function getAuthorType($message)
    {
        return $message->getAuthor()->isVendor()
            ? static::AUTHOR_TYPE_VENDOR
            : parent::getAuthorType($message);
    }
}
