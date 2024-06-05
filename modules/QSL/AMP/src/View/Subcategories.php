<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Subcategories list
 *
 * @ListChild (list="amp.center.bottom", weight="100")
 */
class Subcategories extends \XLite\View\Subcategories
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/AMP/subcategories/' . $this->getDisplayMode();
    }
}
