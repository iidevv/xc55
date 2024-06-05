<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * ____description____
 */
class NewTab extends \XLite\View\Button\Link
{
    /**
     * JavaScript: this code will be used by default
     *
     * @return string
     */
    protected function getDefaultJSCode($action = null)
    {
        return 'window.open(\'' . $this->getParam(self::PARAM_LOCATION) . '\', \'_blank\');';
    }
}
