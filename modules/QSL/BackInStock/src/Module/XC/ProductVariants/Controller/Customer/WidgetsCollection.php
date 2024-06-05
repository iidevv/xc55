<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class WidgetsCollection extends \XLite\Controller\Customer\WidgetsCollection
{
    /**
     * Product variant
     *
     * @var \XC\ProductVariants\Model\ProductVariant
     */
    protected $variant;

    /**
     * Return current product variant Id
     *
     * @return integer
     */
    public function getVariantId()
    {
        return \XLite\Core\Request::getInstance()->variant_id;
    }

    /**
     * Alias
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    public function getProductVariant()
    {
        if (!isset($this->variant)) {
            $this->variant = $this->defineProductVariant();
        }

        return $this->variant;
    }

    /**
     * Define product variant
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    protected function defineProductVariant()
    {
        return \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
            ->findOneBy([
                'variant_id' => $this->getVariantId()
            ]);
    }
}
