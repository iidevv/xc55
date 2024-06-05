<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\Amazon\PayWithAmazon\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("Amazon\PayWithAmazon")
 */
class AmazonCheckout extends \Amazon\PayWithAmazon\View\AmazonCheckout
{
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'css/less/checkout.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
