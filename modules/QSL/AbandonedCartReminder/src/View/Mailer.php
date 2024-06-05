<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Mailer extends \XLite\View\Mailer
{
    /**
     * Inner mailer initialization from set variables
     *
     * @return void
     */
    protected function initMailFromSet()
    {
        $isRisky = $this->hasRiskyImages();
        if ($isRisky) {
            [$replaceFrom, $replaceTo] = $this->fixImageNames();
        }

        parent::initMailFromSet();

        if ($isRisky) {
            $this->mail->Body = str_replace($replaceFrom, $replaceTo, $this->mail->Body);
        }

        // Add List-Unsubscribe header to cart reminders
        if (
            $this->getNotification()
            && $this->getNotification()->getTemplatesDirectory() === "modules/QSL/AbandonedCartReminder/abandonment_email"
            && ($link = $this->getABCRUnsubscribeLink())
        ) {
            $this->mail->addCustomHeader('List-Unsubscribe', "<{$link}>");
        }
    }

    protected function hasRiskyImages()
    {
        $images = $this->get('images');
        if (empty($images) || ! is_array($images)) {
            return false;
        }

        foreach ($images as $image) {
            if (
                strpos($image['name'], ' ') !== false
                || strpos($image['name'], '%20') !== false
            ) {
                return true;
            }
        }

        return false;
    }

    protected function fixImageNames()
    {
        $images = $this->get('images');
        $replaceFrom = $replaceTo = [];
        foreach ($images as $ik => $image) {
            $oldName = $image['name'];
            $newName = str_replace([ ' ', '%20' ], '_', $image['name']);

            if ($newName !== $oldName) {
                $images[$ik]['name'] = $newName;
                $replaceFrom[] = "cid:{$oldName}@mail.lc";
                $replaceTo[] = "cid:{$newName}@mail.lc";
            }
        }

        // push the fixed images back to "storage"
        $this->set('images', $images);

        return [ $replaceFrom, $replaceTo ];
    }

    /**
     * @inheritDoc
     */
    protected function getNotificationSubject()
    {
        $notification = $this->getNotification();

        return $notification
                && $notification->getTemplatesDirectory() === "modules/QSL/AbandonedCartReminder/abandonment_email"
            ? ''
            : parent::getNotificationSubject();
    }

    /**
     * @inheritDoc
     */
    protected function getNotificationText()
    {
        if (
            $this->getNotification()
            && $this->getNotification()->getTemplatesDirectory() === "modules/QSL/AbandonedCartReminder/abandonment_email"
        ) {
            $this->unsubscribeLink = $this->getABCRUnsubscribeLink();
            return '';
        }

        return parent::getNotificationText();
    }

    protected function getABCRUnsubscribeLink()
    {
        return \XLite\Core\Config::getInstance()->QSL->AbandonedCartReminder->abcr_show_unsubscribe
            ? \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL(
                    'abandoned_cart',
                    'unsubscribe',
                    [ 'email' => $this->get('profile')->getLogin() ],
                    \XLite::CART_SELF
                ),
                \XLite\Core\Config::getInstance()->Security->customer_security
            )
            : '';
    }
}
