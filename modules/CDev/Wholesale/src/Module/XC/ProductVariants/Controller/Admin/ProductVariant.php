<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\Controller\Admin;

use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice;
use CDev\Wholesale\Module\XC\ProductVariants\View\ItemsList\ProductVariantWholesalePrices;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\Controller\Admin\ProductVariant
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
        $list[static::PAGE_WHOLESALE_PRICING] = static::t('Wholesale pricing');

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
        $list[static::PAGE_WHOLESALE_PRICING] = 'modules/CDev/Wholesale/pricing/product_variant.twig';

        return $list;
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionWholesalePricesUpdate()
    {
        $list = new ProductVariantWholesalePrices();
        $list->processQuick();

        // Additional correction to re-define end of subtotal range for each discount
        Database::getRepo(ProductVariantWholesalePrice::class)->correctQuantityRangeEnd($this->getProductVariant());
    }
}
