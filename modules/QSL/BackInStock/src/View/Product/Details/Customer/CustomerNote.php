<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Product\Details\Customer;

use XCart\Extender\Mapping\ListChild;
use QSL\BackInStock\Main;

/**
 * Customer note
 *
 * @ListChild (list="product.details.page.info.form", weight="18")
 */
class CustomerNote extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * @inheritDoc
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-customer-note';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BackInStock/product/details/customer_note/body.twig';
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/BackInStock/customer_box.js';
        if (Main::isCurrentSkin('XC-CrispWhiteSkin')) {
            $list[] = 'modules/QSL/BackInStock/modules/XC/CrispWhiteSkin/customer_box.js';
        }

        return $list;
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
