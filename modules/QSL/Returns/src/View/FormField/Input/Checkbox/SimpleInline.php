<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\FormField\Input\Checkbox;

/**
 * Simple checkbox for inline
 */
class SimpleInline extends \XLite\View\FormField\Input\Checkbox\SimpleInline
{
    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        $result = false;

        if (
            \XLite\Core\Request::getInstance()->target == 'create_return'
            && $this->getOrder()->countItems() === 1
        ) {
            $result = true;
        }

        return $result;
    }
}
