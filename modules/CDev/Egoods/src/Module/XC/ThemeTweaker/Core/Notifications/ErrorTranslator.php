<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class ErrorTranslator extends \XC\ThemeTweaker\Core\Notifications\ErrorTranslator
{
    protected static function getSuitabilityErrors()
    {
        $errors = parent::getSuitabilityErrors();

        $errors['order']['no_egoods'] = 'Order #{{value}} doesn\'t contain any e-goods';

        return $errors;
    }
}
