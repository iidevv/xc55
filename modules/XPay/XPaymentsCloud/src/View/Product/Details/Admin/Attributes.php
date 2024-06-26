<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Product\Details\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Product attributes
 *
 * @Extender\Mixin
 */
abstract class Attributes extends \XLite\View\Product\Details\Admin\Attributes implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XPay/XPaymentsCloud/product/attributes/style.css';

        return $list;
    }
}
