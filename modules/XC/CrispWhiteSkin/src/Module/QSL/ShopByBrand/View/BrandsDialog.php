<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\QSL\ShopByBrand\View;

use XCart\Extender\Mapping\Extender;

/**
 * Central Brands list
 *
 * @Extender\Mixin
 * @Extender\Depend ("QSL\ShopByBrand")
 */
class BrandsDialog extends \QSL\ShopByBrand\View\BrandsDialog
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => 'css/less/subcategories.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        $list[] = [
            'file'  => 'modules/QSL/ShopByBrand/brands_dialog/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
