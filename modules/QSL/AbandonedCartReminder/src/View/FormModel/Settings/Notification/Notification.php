<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\FormModel\Settings\Notification;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After ("XC\ThemeTweaker")
 */
class Notification extends \XLite\View\FormModel\Settings\Notification\Notification
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        return $this->isABCRNotification()
            ? [
                'settings'        => [
                    'usereminderstab' => [
                        'type'     => \XLite\View\FormModel\Type\CaptionType::class,
                    ],
                ],
            ]
            : parent::defineFields();
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        return $this->isABCRNotification()
            ? []
            : parent::getFormButtons();
    }

    /**
     * Return form theme files. Used in template.
     *
     * @return array
     */
    protected function getFormThemeFiles()
    {
        $list = parent::getFormThemeFiles();
        $list[] = 'modules/QSL/AbandonedCartReminder/form_model/settings/notification/notification.twig';

        return $list;
    }

    protected function isABCRNotification()
    {
        return $this->getNotification()
            && $this->getNotification()->getTemplatesDirectory() === 'modules/QSL/AbandonedCartReminder/abandonment_email';
    }

    protected function getRemindersTabUrl()
    {
        return $this->buildURL('cart_reminders');
    }
}
