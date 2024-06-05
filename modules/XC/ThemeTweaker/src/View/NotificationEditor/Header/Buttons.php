<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\NotificationEditor\Header;

use XC\ThemeTweaker\Core\Notifications\Data;
use XC\ThemeTweaker\Core\Notifications\ErrorTranslator;
use XLite\Core\Auth;

class Buttons extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/header/buttons.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getButtonWidgets();
    }


    /**
     * @return Data
     */
    protected function getDataSource()
    {
        return \XLite::getController()->getDataSource();
    }

    /**
     * @return string
     */
    protected function getZone()
    {
        return \XLite\Core\Request::getInstance()->zone === \XLite::ZONE_ADMIN
            ? \XLite::ZONE_ADMIN
            : \XLite::ZONE_CUSTOMER;
    }

    /**
     * @return array
     */
    protected function getButtonWidgets()
    {
        $list = [];

        if ($this->getDataSource()->isEditable()) {
            if ($this->getDataSource()->isAvailable()) {
                $url = $this->buildURL(
                    'notification',
                    '',
                    [
                        'templatesDirectory' => $this->getDataSource()->getDirectory(),
                        'page'               => $this->getZone(),
                        'preview'            => true,
                    ]
                );
                $list['preview_template'] = new \XLite\View\Button\Link(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL => 'Preview full email',
                        \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                        \XLite\View\Button\Link::PARAM_BLANK    => true,
                        \XLite\View\Button\Link::PARAM_LOCATION => $url,
                    ]
                );

                $url = $this->buildURL(
                    'notification',
                    'send_test_email',
                    [
                        'templatesDirectory' => $this->getDataSource()->getDirectory(),
                        'page'               => $this->getZone(),
                    ]
                );
                $list['send_test_email'] = new \XLite\View\Button\Link(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL => static::t('Send to {{email}}', ['email' => Auth::getInstance()->getProfile()->getLogin()]),
                        \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                        \XLite\View\Button\Link::PARAM_LOCATION => $url,
                    ]
                );
            } else {
                $unavailabilityReason = null;

                foreach ($this->getDataSource()->getUnavailableProviders() as $provider) {
                    $unavailabilityReason = ErrorTranslator::translateAvailabilityError($provider);

                    if ($unavailabilityReason) {
                        break;
                    }
                }

                $list['preview_template'] = new \XLite\View\Button\Tooltip(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL          => 'Preview full email',
                        \XLite\View\Button\AButton::PARAM_STYLE          => 'action',
                        \XLite\View\Button\AButton::PARAM_DISABLED       => true,
                        \XLite\View\Button\Tooltip::PARAM_BUTTON_TOOLTIP => $unavailabilityReason ?: null,
                    ]
                );

                $list['send_test_email'] = new \XLite\View\Button\Tooltip(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL          => static::t('Send to {{email}}', ['email' => Auth::getInstance()->getProfile()->getLogin()]),
                        \XLite\View\Button\AButton::PARAM_STYLE          => 'action',
                        \XLite\View\Button\AButton::PARAM_DISABLED       => true,
                        \XLite\View\Button\Tooltip::PARAM_BUTTON_TOOLTIP => $unavailabilityReason ?: null,
                    ]
                );
            }
        }

        return $list;
    }
}
