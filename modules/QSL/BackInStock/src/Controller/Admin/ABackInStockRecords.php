<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Admin;

/**
 * Back in stock records controller
 */
abstract class ABackInStockRecords extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Automated messages');
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $class = $this->getItemsListClass();

        /** @var \QSL\BackInStock\View\ItemsList\Model\ARecord $list */
        $list = new $class();
        $list->processQuick();
    }

    /**
     * Check stock for all products
     */
    protected function doActionCheckStock()
    {
        $resultInventory = $this->checkInventory() > 0;
        $resultPrice = $this->checkPrices() > 0;

        if ($resultInventory || $resultPrice) {
            \XLite\Core\Database::getEM()->flush();
        } else {
            \XLite\Core\TopMessage::addInfo('The records check has been completed successfully');
        }
    }

    /**
     * Check 'back in stock' subscriptions
     *
     * @return integer
     */
    protected function checkInventory()
    {
        /** @var \QSL\BackInStock\Model\Repo\Record $repo */
        $repo = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record');

        $updated = $repo->checkWaiting();
        if ($updated > 0) {
            \XLite\Core\TopMessage::addInfo(
                'N subscriptions have been added to the waiting list for "Back in stock" message send-out.',
                ['count' => $updated]
            );
        }

        return $updated;
    }

    /**
     * Check 'price drop' subscription
     *
     * @return integer
     */
    protected function checkPrices()
    {
        /** @var \QSL\BackInStock\Model\Repo\RecordPrice $repo */
        $repo = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice');

        $updated = $repo->checkWaiting();
        if ($updated > 0) {
            \XLite\Core\TopMessage::addInfo(
                'N subscriptions have been added to the waiting list for "Price drop" message send-out.',
                ['count' => $updated]
            );
        }

        return $updated;
    }

    /**
     * Send all ready but un-sent messages
     */
    protected function doActionSendAll()
    {
        $resultInventory = $this->sendInventory();
        $resultPrices = $this->sendPrices();

        if ($resultInventory || $resultPrices) {
            \XLite\Core\Database::getEM()->flush();
        } else {
            \XLite\Core\TopMessage::addInfo('The records check has been completed successfully');
        }
    }

    /**
     * Send 'back in stock' messages
     *
     * @return boolean
     */
    protected function sendInventory()
    {
        /** @var \QSL\BackInStock\Model\Repo\Record $repo */
        $repo = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record');

        [$sent, $bounced] = $repo->sendNotifications();
        if ($sent || $bounced) {
            if ($sent) {
                \XLite\Core\TopMessage::addInfo(
                    'N "Back in stock" messages have been sent',
                    ['count' => $sent]
                );
            }

            if ($bounced) {
                \XLite\Core\TopMessage::addWarning(
                    'There have been N attempts to send out "Back in stock" messages, but the messages could not be sent.',
                    ['count' => $bounced]
                );
            }
        }

        return $sent || $bounced;
    }

    /**
     * Send 'price drop' messages
     *
     * @return boolean
     */
    protected function sendPrices()
    {
        /** @var \QSL\BackInStock\Model\Repo\RecordPrice $repo */
        $repo = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice');

        [$sent, $bounced] = $repo->sendNotifications();
        if ($sent || $bounced) {
            if ($sent) {
                \XLite\Core\TopMessage::addInfo(
                    'N "Price drop" messages have been sent',
                    ['count' => $sent]
                );
            }

            if ($bounced) {
                \XLite\Core\TopMessage::addWarning(
                    'There have been N attempts to send out "Price drop" messages, but the messages could not be sent.',
                    ['count' => $bounced]
                );
            }
        }

        return $sent || $bounced;
    }
}
