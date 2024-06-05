<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Customer\Layout\Content;

use XCart\Extender\Mapping\ListChild;
use XLite\View\ListContainer;

/**
 * Breadcrumbs
 *
 * @ListChild (list="center.top", zone="customer", weight="1000")
 */
class Breadcrumbs extends ListContainer
{
    public function getDefaultTemplate(): string
    {
        return 'layout/content/breadcrumbs.twig';
    }
}
