<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Pager\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\Pager\APager
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return static::isAMP() ? 'modules/QSL/AMP/pager/body.twig' : parent::getDefaultTemplate();
    }
}
