<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\WebmasterKit")
 */
class TemplatesDebugger extends \XC\WebmasterKit\Core\TemplatesDebugger
{
    protected function getMarkTemplatesFlag()
    {
        return parent::getMarkTemplatesFlag()
            && !ThemeTweaker::getInstance()->isInWebmasterMode();
    }
}
