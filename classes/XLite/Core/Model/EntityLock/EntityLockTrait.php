<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model\EntityLock;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use XCart\Container;
use XLite\Core\Database;

trait EntityLockTrait
{
    /**
     * @param string $type
     *
     * @return integer
     */
    abstract protected function getLockTTL($type = 'lock');

    /**
     * @param string       $type
     * @param integer|null $ttl
     */
    public function setEntityLock($type = 'lock', $ttl = null)
    {
        Database::getCacheDriver()->save(
            $this->getLockIdentifier($type),
            \LC_START_TIME + ($ttl ?: $this->getLockTTL($type))
        );
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    public function isEntityLocked($type = 'lock')
    {
        return (int) Database::getCacheDriver()->fetch($this->getLockIdentifier($type)) > 0;
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    public function isEntityLockExpired($type = 'lock')
    {
        $lockExpiration = (int) Database::getCacheDriver()->fetch($this->getLockIdentifier($type));

        return $lockExpiration > 0 && $lockExpiration < \LC_START_TIME;
    }

    /**
     * @param string $type
     */
    public function unsetEntityLock($type = 'lock')
    {
        Database::getCacheDriver()->delete($this->getLockIdentifier($type));
    }

    /**
     * Another type of locking based on https://symfony.com/doc/5.4/components/lock.html#automatically-releasing-the-lock
     *
     * Don't inline the $lock variable https://github.com/symfony/symfony/issues/32062#issuecomment-502681215
     * the code like `$transaction->createEntityAutoLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN)->acquire();` won't work
     */
    public function createEntityAutoLock($type = 'lock')
    {
        // The lock is automatically released when the lock object goes out of scope and is freed by the garbage collector (for example when the PHP process ends):
        $store   = Container::getContainer()->get('xcart.lock.store.factory');
        $factory = new LockFactory($store);

        // Use md5 as there is substr(...,0,50) in \Symfony\Component\Lock\Store\FlockStore::lock(), can be removed for other stores
        return $factory->createLock(md5($this->getLockIdentifier($type)));
    }

    /**
     * @param mixed|null $identifierData
     *
     * @return string
     * @throws \Exception
     */
    protected function getLockIdentifier($identifierData = null)
    {
        return 'EntityLock_'
            . $this->getEntityName()
            . $this->getUniqueIdentifier()
            . ($identifierData ? ('-' . md5(serialize($identifierData))) : '');
    }
}
