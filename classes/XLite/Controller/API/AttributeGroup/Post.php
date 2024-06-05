<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\AttributeGroup;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\Model\AttributeGroup;
use XLite\Model\ProductClass;

final class Post
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AttributeGroup $data, int $class_id): AttributeGroup
    {
        $repository = $this->getRepository();

        /** @var ProductClass $productClass */
        $productClass = $repository->find($class_id);
        if (!$productClass) {
            throw new \InvalidArgumentException(sprintf('Product class with ID %d not found', $class_id));
        }

        $data->setProductClass($productClass);

        return $data;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(ProductClass::class);
    }
}
