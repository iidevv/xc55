<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        static::compose(
            $this,
            [
                'prices_and_inventory' => [
                    'price' => [
                        'participate_sale' => $object->getParticipateSale(),
                        'sale_price'      => [
                            'type'  => $object->getDiscountType(),
                            'value' => $object->getSalePriceValue(),
                        ],
                    ],
                ],
            ]
        );

        $assignedDiscounts = [];
        foreach ($object->getSaleDiscountProducts() as $saleDiscountProduct) {
            $assignedDiscounts[] = $saleDiscountProduct->getSaleDiscount()->getId();
        }

        $this->prices_and_inventory->group_discounts = $assignedDiscounts;
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $participateSale = static::deCompose($this, 'prices_and_inventory', 'price', 'participate_sale');
        $object->setParticipateSale((bool) $participateSale);

        $salePrice = static::deCompose($this, 'prices_and_inventory', 'price', 'sale_price');
        $object->setDiscountType((string) $salePrice['type']);
        $object->setSalePriceValue((float) $salePrice['value']);

        $this->assignProductSpecificSaleDiscounts($object);

        parent::populateTo($object, $rawData);
    }

    protected function assignProductSpecificSaleDiscounts($object)
    {
        $discountIds = $this->prices_and_inventory->group_discounts;

        $object->replaceSpecificProductSaleDiscounts($discountIds);
    }
}
