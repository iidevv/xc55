<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\BackInStock\Main;

/**
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/QSL/BackInStock/customer_note.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        $list[] = [
            'file'  => 'modules/QSL/BackInStock/customer_box.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        if (Main::isCurrentSkin('XC-CrispWhiteSkin')) {
            $list[] = [
                'file'  => 'modules/QSL/BackInStock/modules/XC/CrispWhiteSkin/customer_box.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
        }

        return $list;
    }
}
