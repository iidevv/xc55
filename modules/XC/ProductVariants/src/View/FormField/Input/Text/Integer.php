<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\FormField\Input\Text;

/**
 * Integer
 */
class Integer extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitize()
    {
        return $this->getValue() !== '' ? parent::sanitize() : '';
    }
}
