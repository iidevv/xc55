<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Page\Admin;

class OrderReturns extends \XLite\View\AView
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/Returns/returns/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return bool
     */
    protected function isSearchVisible(): bool
    {
        return true;
    }
}
