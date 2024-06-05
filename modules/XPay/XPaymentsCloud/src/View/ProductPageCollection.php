<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View;

use XCart\Extender\Mapping\Extender;

/**
 * Product page widgets collection
 *
 * @Extender\Mixin
 */
abstract class ProductPageCollection extends \XLite\View\ProductPageCollection implements \XLite\Base\IDecorator
{
    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        $widgets = parent::defineWidgetsCollection();

        $widgets = array_merge(
            $widgets,
            array(
                '\XPay\XPaymentsCloud\View\Product\BuyWithWalletPriceUpdate',
            )
        );

        return array_unique($widgets);
    }
}
