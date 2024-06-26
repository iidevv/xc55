<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * Simple checkbox
 */
class Simple extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return parent::isChecked() || $this->getValue() == 'Y' || $this->getValue() === '1';
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        unset($list['value']);

        return $list;
    }
}
