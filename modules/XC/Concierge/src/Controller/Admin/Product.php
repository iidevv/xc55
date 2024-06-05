<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Controller\Admin\Product
{
    /**
     * return string
     */
    public function getConciergeCategory()
    {
        return 'Product';
    }

    /**
     * @return string
     */
    public function getConciergeTitle()
    {
        $pages = $this->getPages();
        $page = $this->getPage();

        $spages = $this->getSPages();
        $spage  = $this->spage;

        $result = is_array($pages[$page]) ? $pages[$page]['title'] : $pages[$page];

        if (isset($spages[$spage])) {
            $result .= ': ' . $spages[$spage];
        } elseif (isset($spage)) {
            $result .= ': ' . $spage;
        }

        return 'Product: ' . $result;
    }
}
