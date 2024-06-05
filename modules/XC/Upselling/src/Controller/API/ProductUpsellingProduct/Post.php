<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\Controller\API\ProductUpsellingProduct;

use XC\Upselling\Model\UpsellingProduct as Model;
use InvalidArgumentException;
use XLite\Model\Product;
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

        $data->setParentProduct($product);

        return $data;
    }
}
