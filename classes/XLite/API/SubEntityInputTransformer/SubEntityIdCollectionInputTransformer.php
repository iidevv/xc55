<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubEntityInputTransformer;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use XLite\API\Helper\IdGetterInterface;

class SubEntityIdCollectionInputTransformer implements SubEntityIdCollectionInputTransformerInterface
{
    protected EntityRepository $repository;

    protected IdGetterInterface $idGetter;

    protected string $name;

    public function __construct(
        EntityRepository $repository,
        IdGetterInterface $idGetter,
        string $name
    ) {
        $this->repository = $repository;
        $this->idGetter = $idGetter;
        $this->name = $name;
    }

    public function update(Collection $collection, array $idList): void
    {
        $collectionIdList = [];
        $collectionIdHash = [];
        foreach ($collection as $subEntity) {
            $collectionId = $this->idGetter->getId($subEntity);
            $collectionIdList[] = $collectionId;
            $collectionIdHash[$collectionId] = $subEntity;
        }

        // Add
        $needAdd = array_diff($idList, $collectionIdList);
        foreach ($needAdd as $id) {
            $subEntity = $this->repository->find($id);
            if (!$subEntity) {
                throw new InvalidArgumentException(sprintf('%s with ID %d not found', $this->name, $id));
            }

            $collection->add($subEntity);
        }

        // Remove
        $needRemove = array_diff($collectionIdList, $idList);
        foreach ($needRemove as $id) {
            $collection->removeElement($collectionIdHash[$id]);
        }
    }
}
