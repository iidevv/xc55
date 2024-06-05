<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use XC\CustomProductTabs\API\Resource\CustomProductTab as CustomProductTabResource;
use XC\CustomProductTabs\Model\Product\CustomGlobalTab;

class DataPersister implements ContextAwareDataPersisterInterface
{
    private EntityRepository $repository;

    private DataTransformerInterface $inputTransformer;

    private DataTransformerInterface $outputTransformer;

    public function __construct(
        EntityManagerInterface $entityManager,
        DataTransformerInterface $inputTransformer,
        DataTransformerInterface $outputTransformer
    ) {
        $this->inputTransformer = $inputTransformer;
        $this->outputTransformer = $outputTransformer;
        $this->repository = $entityManager->getRepository(CustomGlobalTab::class);
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof CustomProductTabResource;
    }

    /**
     * @param CustomProductTabResource $data
     *
     * @return CustomProductTabResource
     * @throws NonUniqueResultException
     */
    public function persist($data, array $context = []): object
    {
        $model = isset($data->id) ? $this->getModelById($data->id) : null;
        if (!$model) {
            $model = new CustomGlobalTab();
        }

        $context = [
            AbstractItemNormalizer::OBJECT_TO_POPULATE => $model,
        ];
        $model = $this->inputTransformer->transform($data, CustomGlobalTab::class, $context);
        $model->create();

        return $this->outputTransformer->transform($model, CustomProductTabResource::class);
    }

    /**
     * @param CustomProductTabResource $data
     *
     * @throws NonUniqueResultException
     */
    public function remove($data, array $context = []): void
    {
        $model = $this->getModelById($data->id);
        if ($model) {
            $model->getGlobalTab()->delete();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    protected function getModelById(int $id): ?CustomGlobalTab
    {
        return $this->repository->createQueryBuilder('ct')
            ->innerJoin('ct.global_tab', 'gt')
            ->andWhere('gt.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
