<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Controller extends \XLite\View\Controller
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return static::isAMP() ? 'modules/QSL/AMP/body.twig' : parent::getDefaultTemplate();
    }
}
