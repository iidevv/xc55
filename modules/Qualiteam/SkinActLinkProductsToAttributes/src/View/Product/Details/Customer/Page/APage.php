<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * APage
 * @Extender\Mixin
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActLinkProductsToAttributes/product/attribute_value/select.js';

        return $list;
    }


    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            [
                'file'  => 'modules/Qualiteam/SkinActLinkProductsToAttributes/product/attribute_value/style.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ],
        ]);
    }

}