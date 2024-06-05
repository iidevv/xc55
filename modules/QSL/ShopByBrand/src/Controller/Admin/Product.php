<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\AttributeValue\Multiple;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    /**
     * @return \XLite\Model\Product|null
     */
    protected function getAddedProduct()
    {
        $productId = 0;
        $result = null;
        $parsedUrl = parse_url($this->returnURL);
        if (isset($parsedUrl['query'])) {
            array_map(
                static function ($item) use (&$productId) {
                    $item = explode('=', $item);
                    if ($item[0] === 'product_id' && count($item) === 2) {
                        $productId = (int) $item[1];
                    }
                },
                explode('&', $parsedUrl['query'])
            );
        }
        if ($productId !== 0) {
            $result = Database::getRepo('XLite\Model\Product')->findOneBy([ 'product_id' => $productId ]);
        }
        return $result;
    }

    /**
     * @throws \Exception
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();
        if (!Request::getInstance()->product_id) {
            $brandId = (int) Request::getInstance()->brand_id;
            if ($brandId) {
                /** @var Attribute $brandAttribute */
                $brandAttribute = Database::getRepo(Attribute::class)->findBrandAttribute();
                if ($brandAttribute) {
                    /** @var \QSL\ShopByBrand\Model\Brand $brand */
                    $brand = Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneBy([
                        'brand_id' => $brandId
                    ]);
                    $addedProduct = $this->getAddedProduct();
                    if ($brand && $addedProduct) {
                        $attrValue    = new AttributeValueSelect();
                        $attrValue->setProduct($addedProduct);
                        $attrValue->setAttributeOption($brand->getOption());
                        $attrValue->setAttribute($brandAttribute);
                        $attrValue->setPriceModifierType(Multiple::TYPE_ABSOLUTE);
                        $attrValue->setWeightModifierType(Multiple::TYPE_ABSOLUTE);
                        $entityManager = Database::getEM();
                        $entityManager->persist($attrValue);
                        $entityManager->flush();
                    }
                }
            }
        }
    }
}
