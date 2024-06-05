<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Vote bar widget
 *
 * @ListChild (list="amp.head", weight="2")
 */
class AmpComponents extends \XLite\View\AResourcesContainer
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/AMP/amp_components';
    }
}
