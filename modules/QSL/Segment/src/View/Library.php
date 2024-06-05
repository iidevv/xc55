<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="body", zone="customer")
 */
class Library extends \QSL\Segment\View\Initialization
{
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/Segment/library.twig';
    }
}
