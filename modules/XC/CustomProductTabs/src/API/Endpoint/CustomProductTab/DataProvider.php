<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use XC\CustomProductTabs\API\Endpoint\CustomProductTab\ResourceTransformer\OutputTransformerInterface;
use XC\CustomProductTabs\API\Resource\CustomProductTab as CustomProductTabResource;
use XC\CustomProductTabs\Model\Product\CustomGlobalTab;

class DataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{
    private OutputTransformerInterface $transformer;

    private EntityRepository $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        OutputTransformerInterface $transformer
    ) {
        $this->transformer = $transformer;
        $this->repository = $entityManager->getRepository(CustomGlobalTab::class);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === CustomProductTabResource::class;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $qb = $this->repository->createQueryBuilder('ct');

        /** @var CustomGlobalTab $model */
        foreach ($qb->getQuery()->getResult() as $k => $model) {
            yield $k => $this->transformer->transform($model, CustomProductTabResource::class);
        }
    }

    /**
     * @return CustomProductTabResource|null
     * @throws NonUniqueResultException
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?object
    {
        $model = $this->repository->createQueryBuilder('ct')
            ->innerJoin('ct.global_tab', 'gt')
            ->andWhere('gt.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $model ? $this->transformer->transform($model, CustomProductTabResource::class) : null;
    }
}
