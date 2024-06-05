<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\Controller\API\ProductVariantWholesalePrice;

use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice as Model;
use InvalidArgumentException;
use XC\ProductVariants\Model\ProductVariant;
use XC\ProductVariants\Model\Repo\ProductVariant as ProductVariantRepo;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend("XC\ProductVariants")
 */
final class Post
{
    protected ProductVariantRepo $repository;

    public function __construct(
        ProductVariantRepo $repository
    ) {
        $this->repository = $repository;
    }

    public function __invoke(Model $data, int $product_id, int $variant_id): Model
    {
        /** @var ProductVariant $variant */
        $variant = $this->repository->find($variant_id);
        if (!$variant) {
            throw new InvalidArgumentException(sprintf('Product variant with ID %d not found', $variant_id));
        }

        if (
            class_exists('CDev\Sale\Model\ProductVariant')
            && $variant->getDiscountType() === \CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PRICE
            && !$variant->getDefaultSale()
        ) {
            throw new InvalidArgumentException('Cannot create a wholesale price for a variant with an absolute sale price');
        }

        if ($variant->getDefaultPrice()) {
            throw new InvalidArgumentException('Cannot create a wholesale price for a variant with the default price');
        }

        $data->setProductVariant($variant);

        return $data;
    }
}
