<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\API\ProductVariant;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use XC\ProductVariants\Model\Product;
use XC\ProductVariants\Model\ProductVariant as Model;
use XLite\Core\Database;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueCheckbox;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Repo\Product as ProductRepo;

final class Post
{
    protected ProductRepo $repository;

    public function __construct(
        ProductRepo $repository
    ) {
        $this->repository = $repository;
    }

    public function __invoke(Model $data, int $product_id): Model
    {
        /** @var Product $product */
        $product = $this->repository->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf('Product with ID %d not found', $product_id));
        }

        $data->setProduct($product);

        $this->updateVariantsAttributes($product, $data);

        self::setDefaultVariant($product);

        return $data;
    }

    protected function updateVariantsAttributes(Product $product, Model $variant): void
    {
        $attr = $this->getVariantAttributeIds($variant);

        /** @var Attribute $attribute */
        foreach ($product->getVariantsAttributes() as $attribute) {
            if (!$attr || !in_array($attribute->getId(), $attr)) {
                $attribute->getVariantsProducts()->removeElement($product);
                $product->getVariantsAttributes()->removeElement($attribute);
            }
        }

        if ($attr) {
            $attributes = Database::getRepo(Attribute::class)->findByIds($attr);
            foreach ($attributes as $attribute) {
                if (!$product->getVariantsAttributes()->contains($attribute)) {
                    $product->addVariantsAttributes($attribute);
                }
            }
        }

        $product->checkVariants();
    }

    protected function getVariantAttributeIds(Model $variant): array
    {
        $ids = [];

        /** @var AttributeValueCheckbox $avc */
        foreach ($variant->getAttributeValueC() as $avc) {
            $ids[] = $avc->getAttribute()->getId();
        }

        /** @var AttributeValueSelect $avs */
        foreach ($variant->getAttributeValueS() as $avs) {
            $ids[] = $avs->getAttribute()->getId();
        }

        return $ids;
    }

    public static function setDefaultVariant(Product $product)
    {
        if ($product->hasVariants()) {
            $defaultVariant = Database::getRepo('\XC\ProductVariants\Model\ProductVariant')->findOneBy(
                [
                    'product'      => $product,
                    'defaultValue' => true,
                ]
            );

            if (!$defaultVariant) {
                $defaultVariant = $product->getVariants()->first();
                $defaultVariant->setDefaultValue(true);
            }
        }
    }
}
