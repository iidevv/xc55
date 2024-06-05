<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model\EntityVersion;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Uid\Uuid;
use XLite\Core\Lock\SynchronousTrait;

/**
 * Entity version updater subscribes to Doctrine events to update entity version UUIDs automatically on persist and update actions.
 */
class EntityVersionUpdater implements EventSubscriberInterface
{
    use SynchronousTrait;

    /**
     * Entity classes that were flushed during the previous EntityManager#flush() method call. To be used in a postFlush event handler to bump entity type versions.
     *
     * @var array
     */
    protected $flushedEntityTypes = [];

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
            Events::postFlush,
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * onFlush event handler
     *
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $event->getEntityManager();
        /* @var $uow \Doctrine\ORM\UnitOfWork */
        $uow = $em->getUnitOfWork();

        $types = [];

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $type = ClassUtils::getClass($entity);

            if (!isset($types[$type])) {
                $types[$type] = $type;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $type = ClassUtils::getClass($entity);

            if (!isset($types[$type])) {
                $types[$type] = $type;
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $type = ClassUtils::getClass($entity);

            if (!isset($types[$type])) {
                $types[$type] = $type;
            }
        }

        unset($types['XLite\Model\EntityTypeVersion']);

        $this->flushedEntityTypes = $types;
    }

    /**
     * postFlush event handler
     *
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->bumpEntityTypeVersions($this->flushedEntityTypes);
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $instance = $eventArgs->getEntity();

        if ($instance instanceof EntityVersionInterface) {
            $instance->setEntityVersion(Uuid::v4());
        }
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $instance = $eventArgs->getEntity();

        if ($instance instanceof EntityVersionInterface) {
            $instance->setEntityVersion(Uuid::v4());
        }
    }

    /**
     * Bump versions for multiple entity types
     *
     * @param $entities
     */
    protected function bumpEntityTypeVersions($entities)
    {
        foreach ($entities as $entity) {
            $this->bumpEntityTypeVersion($entity);
        }
    }

    /**
     * Bump version for a specific entity type
     *
     * @param $entityType
     * @throws \Exception
     */
    public function bumpEntityTypeVersion($entityType)
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\EntityTypeVersion');
        $newVersion = Uuid::v4();

        $this->retryOnException(static function () use ($entityType, $newVersion, $repo) {
            $repo->replaceEntityTypeVersion($entityType, $newVersion);
            $repo->setEntityTypeVersion($entityType, $newVersion);

            $repo->setBumpedEntityType($entityType);
        });
    }
}
