<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\FormModel\Settings\Notification;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Core\Notifications\Data;
use XC\ThemeTweaker\Core\Notifications\ErrorTranslator;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
class Notification extends \XLite\View\FormModel\Settings\Notification\Notification
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            ['modules/XC/ThemeTweaker/notification/style.less']
        );
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $result = parent::defineFields();

        if (isset($result['scheme']['body'])) {
            $result['scheme']['body'] = array_replace(
                $result['scheme']['body'],
                [
                    'type' => 'XC\ThemeTweaker\View\FormModel\Type\NotificationBodyType',
                    'url'  => $this->getEditBodyURL(),
                ]
            );

            if ($this->isBodyEditable()) {
                $result['scheme']['body']['help'] = sprintf(
                    "<span style='word-break: break-word'>%s</span><br><br>%s",
                    $this->getDataObject()->scheme->body,
                    $result['scheme']['body']['help']
                );
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getEditBodyURL()
    {
        if ($this->isBodyEditable()) {
            $templatesDirectory = $this->getDataObject()->default->templatesDirectory;

            $zone = $this->getDataObject()->default->page === 'admin'
                ? \XLite::ZONE_ADMIN
                : \XLite::ZONE_CUSTOMER;

            return $this->buildURL(
                'notification_editor',
                '',
                [
                    'templatesDirectory' => $templatesDirectory,
                    'zone'          => $zone,
                ]
            );
        }

        return '';
    }

    /**
     * @return Data
     */
    protected function getDataSource()
    {
        return \XLite::getController()->getDataSource();
    }

    /**
     * @return boolean
     */
    protected function isBodyEditable()
    {
        return $this->getDataSource()->isEditable();
    }

    /**
     * @return boolean
     */
    protected function isBodyAvailable()
    {
        return $this->getDataSource()->isAvailable();
    }

    /**
     * Return form theme files. Used in template.
     *
     * @return array
     */
    protected function getFormThemeFiles()
    {
        $list = parent::getFormThemeFiles();
        $list[] = 'modules/XC/ThemeTweaker/form_model/settings/notification/notification.twig';

        return $list;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        if (!$this->isBodyAvailable()) {
            $unavailabilityReason = null;

            foreach ($this->getDataSource()->getUnavailableProviders() as $provider) {
                $unavailabilityReason = ErrorTranslator::translateAvailabilityError($provider);

                if ($unavailabilityReason) {
                    break;
                }
            }

            $result['preview'] = new \XLite\View\Button\Tooltip(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL          => 'Preview full email',
                    \XLite\View\Button\AButton::PARAM_STYLE          => 'action',
                    \XLite\View\Button\AButton::PARAM_DISABLED       => true,
                    \XLite\View\Button\Tooltip::PARAM_BUTTON_TOOLTIP => $unavailabilityReason ?: null,
                ]
            );

            $result['send_test_email'] = new \XLite\View\Button\Tooltip(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL          => static::t('Send to {{email}}', ['email' => Auth::getInstance()->getProfile()->getLogin()]),
                    \XLite\View\Button\AButton::PARAM_STYLE          => 'action',
                    \XLite\View\Button\AButton::PARAM_DISABLED       => true,
                    \XLite\View\Button\Tooltip::PARAM_BUTTON_TOOLTIP => $unavailabilityReason ?: null,
                ]
            );
        }

        return $result;
    }
}
