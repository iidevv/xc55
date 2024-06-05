<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\View\Model;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Model\Attribute;
use XLite\Model\OrderItem;
use XLite\Model\OrderItem\AttributeValue;
use XLite\Model\Product;
use XLite\Model\WidgetParam\TypeObject;

class Add2Cart extends \XLite\View\Model\AModel
{
    use ExecuteCachedTrait;

    public const PARAM_PRODUCT = 'product';

    protected function getDefaultModelObject()
    {
    }

    protected function getFormClass()
    {
        return \Qualiteam\SkinActProductReOrdering\View\Form\ReOrder\Add2Cart::class;
    }

    protected function getFormButtons()
    {
        $result  = parent::getFormButtons();
        $product = $this->getProduct();

        if ($product) {
            $result['submit'] = new \Qualiteam\SkinActProductReOrdering\View\Button\Add2Cart(
                [
                    'product' => $product,
                ]
            );
        }

        return $result;
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT => new TypeObject(
                'Product',
                null,
                false,
                Product::class
            ),
        ];
    }

    protected function getFormWidgetParams()
    {
        $product = $this->getProduct();

        if ($product) {
            $params = [
                'formParams' => [
                    'product_id'  => $product->getProductId(),
                    'category_id' => $product->getCategoryId(),
                ],
            ];

            /** @var OrderItem $productOrderItem */
            $productOrderItem  = $product->getLastOrderItem();
            $productAttributes = $productOrderItem->getAttributeValues();

            if ($productAttributes) {

                /** @var AttributeValue $productAttribute */
                foreach ($productAttributes as $productAttribute) {
                    $defaultAttribute = null;

                    if (!$productAttribute->getAttributeValueId()) {
                        /** @var Attribute $attribute */
                        $attribute = $this->getAttribute($productAttribute);
                        $defaultAttribute = $attribute?->getDefaultAttributeValue($product);
                    }

                    $attributeValueId = $defaultAttribute && $defaultAttribute->getId()
                        ? $defaultAttribute->getId()
                        : $productAttribute->getAttributeValueId();

                    $params['formParams']['attribute_values[' . $productAttribute->getAttributeId() . ']'] = $attributeValueId;
                }
            }

            return $params;
        }

        return parent::getFormWidgetParams();
    }

    protected function getContainerClass()
    {
        return 'reorder-product-properties';
    }

    protected function getAttribute($productAttribute)
    {
        return $this->executeCachedRuntime(static function () use ($productAttribute) {
            return Database::getRepo(Attribute::class)
                ->findOneBy(['id' => $productAttribute->getAttributeId()]);
        }, [
            __METHOD__,
            self::class,
            $productAttribute->getAttributeId(),
        ]);
    }
}
