<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Product;

use XCart\Extender\Mapping\Extender;
use QSL\AMP\Core\HtmlToAmpConverter;

/**
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Get converted AMP product description
     *
     * @return string
     */
    protected function getAmpProductDescription()
    {
        $converter = HtmlToAmpConverter::getInstance();

        return $converter->convert($this->product->getProcessedBriefDescription());
    }

    /**
     * Get product URL
     *
     * @param integer $categoryId Category ID
     *
     * @return string
     */
    protected function getProductURL($categoryId = null)
    {
        $shortUrl = parent::getProductURL($categoryId);

        if (LC_USE_CLEAN_URLS) {
            // relative URLs cause XCB-528, on the other hand we cannot use 'base' tag in skins/customer/modules/QSL/AMP/header/parts/base.twig
            // as it is 'Prohibited' by https://amp.dev/documentation/guides-and-tutorials/learn/spec/amphtml/
            return \XLite::getInstance()->getShopURL(parent::getProductURL($categoryId));
        } else {
            return $shortUrl;
        }
    }
}
