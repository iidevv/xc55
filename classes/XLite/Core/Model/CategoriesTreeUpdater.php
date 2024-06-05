<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use XLite\Model\Category;

class CategoriesTreeUpdater implements EventSubscriberInterface
{
    protected static bool $update = false;

    protected ?Request $request;

    public function __construct(?RequestStack $requestStack)
    {
        $this->request = $requestStack ? $requestStack->getCurrentRequest() : null;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::preUpdate,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $instance = $eventArgs->getEntity();

        if ($instance instanceof Category && $this->isApiRoute()) {
            self::$update = true;
        }
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $instance = $eventArgs->getEntity();

        if ($instance instanceof Category && $this->isApiRoute()) {
            $changeSet = $eventArgs->getEntityChangeSet();

            if (isset($changeSet['parent'])) {
                self::$update = true;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        if (static::$update) {
            /** @var \XLite\Model\Repo\Category $repo */
            $repo = $eventArgs->getEntityManager()->getRepository(Category::class);

            $repo->correctCategoriesStructure();

            static::$update = false;
        }
    }

    protected function isApiRoute(): bool
    {
        return $this->request && substr($this->request->getRequestUri(), 0, 5) === '/api/';
    }
}
