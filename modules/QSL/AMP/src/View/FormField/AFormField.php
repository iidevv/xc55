<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\FormField;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AFormField extends \XLite\View\FormField\AFormField
{
    /**
     * getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $attrs = parent::getAttributes();

        if (static::isAMP() && $this->isRequired()) {
            $attrs['required'] = 'required';
        }

        return $attrs;
    }
}
