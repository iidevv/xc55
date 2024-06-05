<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View;


use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 * @Extender\After("QSL\CloudSearch")
 */
class Controller extends \XLite\View\Controller
{

    protected function getCloudSearchInitData(): array
    {
        $data = parent::getCloudSearchInitData();

        if(isset($data['cloudSearch']['selector'])){
            $data['cloudSearch']['selector'] =
                '.simple-search-product-form input[name="substring"], .search-form input[name="substring"]';
        }

        return $data;
    }
}