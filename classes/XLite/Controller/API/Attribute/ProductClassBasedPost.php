<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\Attribute;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\Model\Attribute;
use XLite\Model\ProductClass;

final class ProductClassBasedPost
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

    public function __invoke(Attribute $data, int $class_id): Attribute
    {
        $repository = $this->getRepository();

        $productClass = $repository->find($class_id);
        if (!$productClass) {
            throw new \InvalidArgumentException(sprintf('Product class with ID %d not found', $class_id));
        }

        $data->setProductClass($productClass);
        $data->setType($this->type);

        if ($data->getAttributeGroup()) {
            $groupProductClassId = $data->getAttributeGroup()->getProductClass()
                ? $data->getAttributeGroup()->getProductClass()->getId()
                : null;
            $entityProductClassId = $data->getProductClass() ? $data->getProductClass()->getId() : null;
            if ($groupProductClassId !== $entityProductClassId) {
                throw  new InvalidArgumentException(
                    sprintf(
                        'Group\'s product class (%d) not equal attribute\'s product class (%d)',
                        $groupProductClassId,
                        $entityProductClassId
                    )
                );
            }
        }

        return $data;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(ProductClass::class);
    }
}
