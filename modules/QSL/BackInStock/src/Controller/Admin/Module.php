<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Module extends \XLite\Controller\Admin\Module
{
    /**
     * Check back in stock records
     *
     * @return void
     */
    protected function doActionCheckBack2stock()
    {
        $updated = false;

        $updatedInventory = $this->checkBackToStockInventory();
        $updatedprices = $this->checkBackToStockPrices();
        if ($updatedInventory > 0 || $updatedprices > 0) {
            \XLite\Core\Database::getEM()->flush();
            $updated = true;
        }

        $sentInventory = $this->sendBackToStockInventory();
        $sentPrices = $this->sendBackToStockPrices();
        if ($sentInventory || $sentPrices) {
            \XLite\Core\Database::getEM()->flush();
            $updated = true;
        }

        if (!$updated) {
            \XLite\Core\TopMessage::addInfo('The records check has been completed successfully');
        }
    }

    /**
     * Check 'back in stock' subscriptions
     *
     * @return integer
     */
    protected function checkBackToStockInventory()
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
    protected function checkBackToStockPrices()
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
     * Send 'back in stock' messages
     *
     * @return boolean
     */
    protected function sendBackToStockInventory()
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
    protected function sendBackToStockPrices()
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
