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

final class ViewListSave
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

    public function __invoke(array $list, string $versionKey): void
    {
        $event = new ViewListEvent($list);

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-lists.save.before');

        $inserted = [];

        foreach ($event->getList() as $key => $data) {
            [$data, $serviceData] = $this->divideListData($data);
            $parentKey = $serviceData['parent'] ?? null;

            if ($parentKey && !isset($inserted[$parentKey])) {
                // todo: throw domain exception
                throw new \Exception('Missing parent record');
            }

            /** @var ViewList $entity */
            $entity = $this->repository->insert(['version' => $versionKey] + $data, false);

            if ($parentKey) {
                $entity->setParent($inserted[$parentKey]);
            }

            $inserted[$key] = $entity;
        }

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-lists.save.after');
    }

    private function divideListData(array $data): array
    {
        $serviceData = [];

        foreach (['parent', 'name'] as $serviceField) {
            if (isset($data[$serviceField])) {
                $serviceData[$serviceField] = $data[$serviceField];
                unset($data[$serviceField]);
            }
        }

        return [$data, $serviceData];
    }
}
