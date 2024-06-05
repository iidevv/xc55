<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("!XC\ProductVariants")
 */
class InfoWholesale extends \XLite\View\FormModel\Product\Info
{
    /**
     * @param array $sections
     *
     * @return array
     */
    protected function prepareFields($sections)
    {
        $result = parent::prepareFields($sections);

        $priceDescription = $this->getDataObject()->default->identity && $this->getPriceDescriptionTemplate()
            ? $this->getWidget([
                'template' => $this->getPriceDescriptionTemplate(),
            ])->getContent()
            : '';

        $result['prices_and_inventory']['price']['description'] = $priceDescription;

        return $result;
    }

    /**
     * @return string
     */
    protected function getPriceDescriptionTemplate()
    {
        /** @var \CDev\Wholesale\Model\Product $product */
        $product = $this->getProductEntity();

        if ($product && $product->isWholesalePricesEnabled() && count($product->getWholesalePrices()) > 0) {
            return 'modules/CDev/Wholesale/form_model/product/info/wholesale_defined_link.twig';
        }

        return '';
    }
}
