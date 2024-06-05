<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\Attribute;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\Model\Attribute;
use XLite\Model\Product;

final class ProductBasedPost
{
    private EntityManagerInterface $entityManager;

    protected string $type;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $type
    ) {
        $this->entityManager = $entityManager;
        $this->type = $type;
    }

    public function __invoke(Attribute $data, int $product_id): Attribute
    {
        $repository = $this->getRepository();

        $product = $repository->find($product_id);
        if (!$product) {
            throw new \InvalidArgumentException(sprintf('Product with ID %d not found', $product_id));
        }

        $data->setProduct($product);
        $data->setType($this->type);

        return $data;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }
}
