<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\Reviews\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract product page
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\Reviews")
 */
class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/Reviews/product/details/style.css';

        return $list;
    }
}
