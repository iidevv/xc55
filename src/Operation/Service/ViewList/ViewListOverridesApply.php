<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Operation\Service\ViewList;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use XCart\Event\Service\ViewListEvent;
use XLite\Model\Repo\ViewList as ViewListRepository;
use XLite\Model\ViewList;

final class ViewListOverridesApply
{
    private EntityManagerInterface $entityManager;

    private ViewListRepository $repository;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;

        /** @var ViewListRepository $repository */
        $repository       = $entityManager->getRepository(ViewList::class);
        $this->repository = $repository;

        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(string $versionKey)
    {
        $event = new ViewListEvent([]);

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-lists.apply-overrides.before');

        $this->restoreOverrides($versionKey);

        // remove all records with version not equal to $versionKey
        $this->repository->deleteObsolete($versionKey);

        // set version as NULL for all records with version equal to $versionKey
        $this->repository->markCurrentVersion($versionKey);

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-lists.apply-overrides.after');
    }

    public function restoreOverrides(string $versionKey): void
    {
        foreach ($this->repository->findOverridden() as $override) {
            $entity = $this->repository->findEqual($override, true);

            if ($entity) {
                // entity already exist, just set override related fields
                $entity->mapOverrides($override);
            } else {
                // create new entity
                $entity = $override->cloneEntity();

                $entity->setVersion($versionKey);
                $entity->setDeleted(!$entity->isViewListModuleEnabled());

                $equalParent = $this->repository->findEqualParent($override->getParent() ?: $entity, true);

                if ($equalParent) {
                    $entity->setParent($equalParent);
                }

                $this->entityManager->persist($entity);
            }
        }

        $this->entityManager->flush();
    }
}
