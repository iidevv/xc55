<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\Page\Admin;

use XCart\Extender\Mapping\ListChild;

/**
 * Quick messages page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ProductStickers extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['product_stickers']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ProductStickers/page/product_stickers/body.twig';
    }
}
