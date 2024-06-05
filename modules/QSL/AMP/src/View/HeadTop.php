<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Displays AMP javascript at the very top of <head> tag
 *
 * @ListChild (list="head", zone="customer", weight="-1")
 */
class HeadTop extends \XLite\View\AView
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isAMP();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/head_top.twig';
    }
}
