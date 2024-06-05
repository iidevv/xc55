<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Settings dialog model widget
 * @Extender\Mixin
 */
abstract class Settings extends \XLite\View\Model\Settings
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (
            $this->getTarget() === 'module'
            && \XLite\Core\Request::getInstance()->moduleId === 'QSL-LoyaltyProgram'
        ) {
            $list[] = 'modules/QSL/LoyaltyProgram/settings/settings.css';
        }

        return $list;
    }
}
