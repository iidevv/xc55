<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Button\Admin\ModifyReturn;

/**
 * Modify return methods
 */
class Submit extends \XLite\View\Button\Regular
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'create_order_return';
    }

    /**
     * getDefaultInfoText
     *
     * @return string
     */
    protected function getDefaultInfoText()
    {
        return 'No items have been selected for return';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        $code = parent::getDefaultJSCode();

        $code = 'if (!$(\'div.selector input[name^="select"]:checked\').length) { alert(\'' . $this->getDefaultInfoText() . '\')} else {' . $code . '}';

        return $code;
    }
}
