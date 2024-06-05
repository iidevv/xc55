<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\Session;
use XLite\Controller\Admin\ProductSelections;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\AttributeValue\Multiple;

/**
 * Adding products to the category.
 */
class BrandProductSelections extends ProductSelections
{
    /**
     * Check if the product id which will be displayed as "Already added"
     *
     * @param integer $productId Product ID.
     *
     * @return boolean
     */
    public function isExcludedProductId($productId)
    {
        $result = false;
        $selectedBrandId = (int)Request::getInstance()->id;
        if ($selectedBrandId) {
            $searchParams = $this->getSessionCellName();
            Session::getInstance()->$searchParams = array_merge(
                Session::getInstance()->$searchParams,
                ['id' => $selectedBrandId]
            );
        } else {
            $selectedBrandId = $this->getCondition('id');
        }
        if ($selectedBrandId) {
            /** @var \QSL\ShopByBrand\Model\Brand $brand */
            $brand = Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneBy([
                'brand_id' => $selectedBrandId
            ]);
            $product = Database::getRepo('XLite\Model\Product')->findOneBy([
                'product_id' => $productId
            ]);
            if ($brand) {
                $option = $brand->getOption();
                $brandAttribute = Database::getRepo(Attribute::class)->findBrandAttribute();
                $attrValue = Database::getRepo(AttributeValueSelect::class)->findOneBy([
                    'product' => $product,
                    'attribute_option' => $option,
                    'attribute' => $brandAttribute
                ]);
                $result = !!$attrValue;
            }
        }
        return $result;
    }

    /**
     * Add selected products to the current brand.
     *
     * @throws \Exception
     */
    protected function doActionUpdate()
    {
        $id = (int) Request::getInstance()->id;
        $items = (array) Request::getInstance()->select;
        if ($items && $id) {
            /** @var \QSL\ShopByBrand\Model\Brand $brand */
            $brand = Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneBy([
                'brand_id' => $id
            ]);
            if ($brand) {
                $option = $brand->getOption();
                $brandAttribute = Database::getRepo(Attribute::class)->findBrandAttribute();
                $entityManager = Database::getEM();
                array_walk(
                    $items,
                    static function ($item, $productId) use ($option, $brandAttribute, $entityManager) {
                        if ($item) {
                            /** @var \XLite\Model\Product|null $product */
                            $product = Database::getRepo('XLite\Model\Product')->findOneBy(
                                ['product_id' => $productId]
                            );
                            if ($product) {
                                $attrValue = new AttributeValueSelect();
                                $attrValue->setProduct($product);
                                $attrValue->setAttributeOption($option);
                                $attrValue->setAttribute($brandAttribute);
                                $attrValue->setPriceModifierType(Multiple::TYPE_ABSOLUTE);
                                $attrValue->setWeightModifierType(Multiple::TYPE_ABSOLUTE);
                                $entityManager->persist($attrValue);
                            }
                        }
                    }
                );
                $entityManager->flush();
            }
        }
        $this->setHardRedirect();
        $this->setReturnURL(
            $this->buildURL(
                'brand_products',
                '',
                $id ? [ 'id' => $id ] : []
            )
        );
    }
}
