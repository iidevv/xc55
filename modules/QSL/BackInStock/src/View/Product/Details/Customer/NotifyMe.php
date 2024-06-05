<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Product\Details\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * NotifyMe box
 *
 * @ListChild (list="product.details.page.info.form.buttons.cart-buttons", weight="25")
 * @ListChild (list="product.details.page.info.form.buttons-added.cart-buttons", weight="35")
 */
class NotifyMe extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * @inheritDoc
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-notify-me';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BackInStock/product/details/notify_me/body.twig';
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && ($product = $this->getProduct())
            && ($product->isBackInStockAllowed() || $product->isPriceDropAllowed());
    }
}
