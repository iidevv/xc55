<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    /**
     * Page key
     */
    public const PAGE_WHOLESALE_PRICING = 'wholesale_pricing';

    /**
     * Get pages
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        if (!$this->isNew()) {
            $list[static::PAGE_WHOLESALE_PRICING] = static::t('Wholesale pricing');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if (!$this->isNew()) {
            $list[static::PAGE_WHOLESALE_PRICING] = 'modules/CDev/Wholesale/pricing/product.twig';
        }

        return $list;
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionWholesalePricesUpdate()
    {
        $list = new \CDev\Wholesale\View\ItemsList\WholesalePrices();
        $list->processQuick();

        \XLite\Core\QuickData::getInstance()->updateProductDataInternal($this->getProduct());
        \XLite\Core\Database::getEM()->flush();

        // Additional correction to re-define end of subtotal range for each discount
        \XLite\Core\Database::getRepo('CDev\Wholesale\Model\WholesalePrice')
            ->correctQuantityRangeEnd($this->getProduct());
    }

    /**
     * Return true if absolute sale setted (wholesale prices are not available)
     *
     * @return bool
     */
    public function isOnAbsoluteSale()
    {
        return $this->getProduct()->getParticipateSale()
            && $this->getProduct()->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE;
    }
}
