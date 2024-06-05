<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class NotifyMe extends \QSL\BackInStock\View\Product\Details\Customer\NotifyMe
{
    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/BackInStock/button/notify_me.js';

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BackInStock/modules/XC/ProductVariants/product/details/notify_me/body.twig';
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        $product = $this->getProduct();
        $variant = $this->getProductVariant();

        // If product must have variants, placeholder should be visible
        $result = parent::isVisible() || $product->mustHaveVariants();
        if ($variant) {
            $initialParentClass = get_parent_class(parent::class);
            $result = $initialParentClass::isVisible();
        }

        return $result;
    }
}
