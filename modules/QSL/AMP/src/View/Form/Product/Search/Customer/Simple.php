<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Form\Product\Search\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Simple extends \XLite\View\Form\Product\Search\Customer\Simple
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return static::isAMP() ? 'modules/QSL/AMP/product/simple_form.twig' : parent::getDefaultTemplate();
    }
}
