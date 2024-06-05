<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class WidgetsCollection extends \XLite\Controller\Customer\WidgetsCollection
{
    /**
     * Product
     *
     * @var \XLite\Model\Product
     */
    protected $product;

    /**
     * Return current product
     *
     * @return integer
     */
    public function getProductId()
    {
        return \XLite\Core\Request::getInstance()->product_id;
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        if (!isset($this->product)) {
            $this->product = $this->defineProduct();
        }

        return $this->product;
    }

    /**
     * Define product
     *
     * @return \XLite\Model\Product
     */
    protected function defineProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->findOneBy([
                'product_id' => $this->getProductId()
            ]);
    }
}
