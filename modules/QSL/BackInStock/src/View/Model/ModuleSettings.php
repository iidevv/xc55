<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ModuleSettings extends \XLite\View\Model\ModuleSettings
{
    /**
     * @inheritdoc
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        if ($this->getModule() === 'QSL-BackInStock') {
            $result['check_back2stock'] = new \XLite\View\Button\Regular(
                [
                    \XLite\View\Button\Regular::PARAM_ACTION => 'check_back2stock',
                    \XLite\View\Button\Regular::PARAM_LABEL  => static::t('Check products'),
                    \XLite\View\Button\Regular::PARAM_STYLE  => 'btn regular-button always-enabled b2s-check-records',
                ]
            );
            $result['edit_notifications'] = new \XLite\View\Button\SimpleLink(
                [
                    \XLite\View\Button\SimpleLink::PARAM_LOCATION => static::buildURL(
                        'notification',
                        null,
                        [
                            'templatesDirectory' => 'modules/QSL/BackInStock/notification',
                        ]
                    ),
                    \XLite\View\Button\SimpleLink::PARAM_LABEL    => static::t('Edit back in stock notification'),
                    \XLite\View\Button\SimpleLink::PARAM_STYLE    => 'action b2s-edit-notification',
                ]
            );
            $result['edit_price_notifications'] = new \XLite\View\Button\SimpleLink(
                [
                    \XLite\View\Button\SimpleLink::PARAM_LOCATION => static::buildURL(
                        'notification',
                        null,
                        [
                            'templatesDirectory' => 'modules/QSL/BackInStock/price_drop_notification',
                        ]
                    ),
                    \XLite\View\Button\SimpleLink::PARAM_LABEL    => static::t('Edit price drop notification'),
                    \XLite\View\Button\SimpleLink::PARAM_STYLE    => 'action b2s-edit-notification',
                ]
            );
        }

        return $result;
    }
}
