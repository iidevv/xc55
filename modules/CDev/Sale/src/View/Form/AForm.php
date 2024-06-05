<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\Form;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AForm extends \XLite\View\Form\AForm
{
    /**
     * Set validators pairs for products data. Sale structure.
     *
     * @param mixed &$data Data
     *
     * @return void
     */
    protected function setSaleDataValidators(&$data)
    {
        if ($this->getPostedData('participateSale')) {
            switch ($this->getPostedData('discountType')) {
                case \CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PRICE:
                    $data->addPair('salePriceValue', new \XLite\Core\Validator\TypeFloat(), null, 'Sale price')
                        ->setRange(0);
                    break;

                case \CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PERCENT:
                    $data->addPair('salePriceValue', new \XLite\Core\Validator\TypeInteger(), null, 'Percent off')
                        ->setRange(1, 100);
                    break;

                default:
            }
        }
    }
}
