<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubEntityInputTransformer;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;

class SubEntityIdInputTransformer implements SubEntityIdInputTransformerInterface
{
    protected EntityRepository $repository;

    protected string $name;

    public function __construct(
        EntityRepository $repository,
        string $name
    ) {
        $this->repository = $repository;
        $this->name = $name;
    }

    public function transform($id): ?object
    {
        if (!$id) {
            return null;
        }

        $subEntity = $this->repository->find($id);
        if (!$subEntity) {
            throw new InvalidArgumentException(sprintf('%s with ID %d not found', $this->name, $id));
        }

        return $subEntity;
    }
}
