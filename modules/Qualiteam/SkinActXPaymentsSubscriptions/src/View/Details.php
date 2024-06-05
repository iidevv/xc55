<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View;

use XCart\Extender\Mapping\Extender;
use XLite\View\Product\Details\Customer\Page\APage;

/**
 * Product details page extension
 *
 * @Extender\Mixin
 */
abstract class Details extends APage
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/product/style.css';

        return $list;
    }
}
