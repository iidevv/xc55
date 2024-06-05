<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Module\XC\ThemeTweaker\Core\Notifications\Data;

use XCart\Extender\Mapping\Extender;

/**
 * Constant
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class Constant extends \XC\ThemeTweaker\Core\Notifications\Data\Constant
{
    public function isAvailable($templateDir)
    {
        return parent::isAvailable($templateDir) && (
                $templateDir !== 'modules/XC/Reviews/new_review'
                || $this->getName($templateDir) !== 'review'
                || $this->getData($templateDir)
            );
    }
}
