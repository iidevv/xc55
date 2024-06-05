<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Amp version of XLite\View\PoweredBy widget
 *
 * @ListChild (list="amp.sidebar.footer", weight="10")
 */
class PoweredBy extends \XLite\View\PoweredBy
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/layout/footer/powered_by.twig';
    }
}
