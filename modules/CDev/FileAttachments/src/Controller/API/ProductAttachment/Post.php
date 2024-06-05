<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\Controller\API\ProductAttachment;

use InvalidArgumentException;
use CDev\FileAttachments\Model\Product\Attachment;
use XLite\Model\Repo\Product as ProductRepo;

final class Post
{
    protected ProductRepo $repository;

    public function __construct(
        ProductRepo $repository
    ) {
        $this->repository = $repository;
    }

    public function __invoke(Attachment $data, int $product_id): Attachment
    {
        $product = $this->repository->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf('Product with ID %d not found', $product_id));
        }

        $data->setProduct($product);

        return $data;
    }
}
