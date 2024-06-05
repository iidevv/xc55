<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Operation\Service\ViewList;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use XCart\Event\Service\ViewListMutationEvent;
use XLite\Model\Repo\ViewList as ViewListRepository;
use XLite\Model\ViewList;

final class ViewListMutationApply
{
    private ViewListRepository $repository;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        /** @var ViewListRepository $repository */
        $repository       = $entityManager->getRepository(ViewList::class);
        $this->repository = $repository;

        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(array $mutations, string $versionKey): array
    {
        $event = new ViewListMutationEvent();
        $event->setVersionKey($versionKey);
        $event->setMutations($mutations);

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-list.apply-mutation.before');

        foreach ($event->getMutations() as $subject => $mutation) {
            foreach ($mutation[ViewListMutationEvent::TO_REMOVE] ?? [] as $removeMutation) {
                $this->repository->removeByCriteria($this->getRemoveCriteria($subject, $removeMutation));
            }

            foreach ($mutation[ViewListMutationEvent::TO_INSERT] ?? [] as $insertMutation) {
                $this->repository->insert(['version' => $versionKey] + $this->getInsertData($subject, $insertMutation));
            }
        }

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-list.apply-mutation.after');

        return $event->getMutations();
    }

    private function getRemoveCriteria(string $subject, array $mutation): array
    {
        $criteria = [];

        if ($this->isTemplate($subject)) {
            $criteria['tpl'] = $subject;
        } else {
            $criteria['child'] = $subject;
        }

        if (isset($mutation[0])) {
            $criteria['list'] = $mutation[0];
        }

        if (isset($mutation[1])) {
            $criteria['interface'] = $mutation[1];
        }

        if (isset($mutation[2])) {
            $criteria['zone'] = $mutation[2];
        }

        return $criteria;
    }

    private function getInsertData(string $subject, array $mutation): array
    {
        $data = [];

        if ($this->isTemplate($subject)) {
            $data['tpl'] = $subject;
        } else {
            $data['child'] = $subject;
        }

        if (isset($mutation[0])) {
            $data['list'] = $mutation[0];
        }

        if (isset($mutation[1])) {
            $data['weight'] = $mutation[1];
        }

        if (isset($mutation[2])) {
            $data['interface'] = $mutation[2];
        }

        if (isset($mutation[3])) {
            $data['zone'] = $mutation[3];
        }

        return $data;
    }

    private function isTemplate(string $subject): bool
    {
        return substr($subject, -5) === '.twig';
    }
}
