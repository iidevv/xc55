<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Helpers\CreateUpdate;

use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;
use XCart\Container;
use XLite\Core\Database;
use XLite\Core\Doctrine\ORM\EntityManager;
use XLite\Model\AEntity;

abstract class ACreateUpdate implements IUpdate
{
    protected ?MessageBusInterface $bus;
    protected ?EntityManager       $em;

    protected array $changes;
    protected bool  $isSkipYotpoUpdate = false;

    abstract protected function do(): void;
    abstract protected function getExcludedKeysOnChange(): array;
    abstract protected function getModelObjectForFindChanges(): AEntity;

    public function __construct()
    {
        $this->prepareBus();
        $this->prepareEntityManager();
        $this->prepareChanges();
        $this->checkExcludedKeys();
    }

    protected function prepareBus(): void
    {
        $this->bus = $this->getBusContainer();
    }

    protected function getBusContainer(): MessageBusInterface|TraceableMessageBus|null
    {
        return Container::getContainer()
            ? Container::getContainer()?->get('messenger.default_bus')
            : null;
    }

    protected function prepareEntityManager(): void
    {
        $this->em = Database::getEM();
    }

    protected function prepareChanges(): void
    {
        $this->changes = $this->getEntityManager()
            ->getUnitOfWork()
            ->getEntityChangeSet($this->getModelObjectForFindChanges());
    }

    protected function getEntityManager(): ?EntityManager
    {
        return $this->em;
    }

    protected function checkExcludedKeys(): void
    {
        foreach ($this->changes as $key => $change) {
            $this->isSkipYotpoUpdate = $this->isExcludedKeyOrChange($key, $change);
        }
    }

    protected function isExcludedKeyOrChange(string $key, array|PersistentCollection $change = []): bool
    {
        return in_array($key, $this->getExcludedKeysOnChange())
            || $this->isExcludedChange($change);
    }

    protected function isExcludedChange(array|PersistentCollection $change): bool
    {
        return false;
    }

    public function execute(): void
    {
        if ($this->hasBus()
            && !$this->isSkipYotpoUpdate()
        ) {
            $this->do();
        }
    }

    protected function hasBus(): bool
    {
        return (bool) $this->getBus();
    }

    protected function getBus()
    {
        return $this->bus;
    }

    protected function isSkipYotpoUpdate(): bool
    {
        return $this->isSkipYotpoUpdate;
    }

}