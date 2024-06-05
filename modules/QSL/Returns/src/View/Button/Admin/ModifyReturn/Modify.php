<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Button\Admin\ModifyReturn;

/**
 * Modify return methods
 */
class Modify extends \XLite\View\Button\Regular
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'modify_order_return';
    }

    /**
     * getDefaultInfoText
     *
     * @return string
     */
    protected function getDefaultInfoText()
    {
        return 'No items have been selected';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        $code = parent::getDefaultJSCode();

        if (\XLite\Core\Request::getInstance()->page == 'create_return') {
            $code = 'if (!$(\'div.selector input[name^="select"]:checked\').length) { alert(\'' . $this->getDefaultInfoText() . '\')} else {' . $code . '}';
        }

        return $code;
    }
}
