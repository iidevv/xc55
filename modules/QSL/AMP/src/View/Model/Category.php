<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Model;

use XCart\Extender\Mapping\Extender;
use XLite\View\Button\AButton;
use XLite\View\Button\Regular;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\View\Model\Category
{
    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        return $result + [
                'update_amp_cache' => new Regular(
                    [
                        AButton::PARAM_LABEL       => static::t('Update Google AMP cache'),
                        AButton::PARAM_BTN_TYPE    => 'regular-button always-enabled',
                        AButton::PARAM_STYLE       => 'action',
                        AButton::PARAM_DISABLED    => false,
                        Regular::PARAM_ACTION      => 'updateGoogleAmpCache',
                    ]
                ),
            ];
    }
}
