<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\FormField\Input;

use QSL\ShopByBrand\Model\Brand;
use QSL\ShopByBrand\Model\BrandProducts;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Product;

/**
 * Order by position
 */
class BrandProductsOrderby extends \XLite\View\FormField\Inline\Input\Text\Position\Move
{
    protected function saveFieldValue(array $field)
    {
        $position = $field['widget']->getValue();
        $brandId = (int)Request::getInstance()->id;
        /** @var Product $product */
        $product = $this->getEntity();
        if ($brandId) {
            /** @var Brand $brand */
            $brand = Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneBy([
                'brand_id' => $brandId
            ]);
            if ($brand) {
                $brandProducts = Database::getRepo('QSL\ShopByBrand\Model\BrandProducts')->findOneBy([
                    'brand' => $brand,
                    'product' => $this->getEntity()
                ]);
                if (!$brandProducts) {
                    $brandProducts = new BrandProducts();
                    $brandProducts->setProduct($product);
                    $brandProducts->setBrand($brand);
                }
                $brandProducts->setOrderby($position);
                $entityManager = Database::getEM();
                $entityManager->persist($brandProducts);
                $entityManager->flush();
            }
        }
    }

    /**
     * Get entity value
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        /** @var Brand|null $brand */
        $brand = Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneBy([
            'brand_id' => (int)Request::getInstance()->id
        ]);
        return $this->getEntity()->getBrandPosition($brand);
    }
}
