<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Controller;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract controller
 * @Extender\Mixin
 */
abstract class AController extends \XLite\Controller\AController
{
    public function isReturnActionsEnabled()
    {
        return \QSL\Returns\Main::isActionsEnabled();
    }
}
