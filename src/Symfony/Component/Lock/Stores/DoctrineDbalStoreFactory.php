<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Symfony\Component\Lock\Stores;

use Symfony\Component\Lock\Store\DoctrineDbalStore;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineDbalStoreFactory
{
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \Exception
     */
    public function getStore(): ?\Symfony\Component\Lock\Store\DoctrineDbalStore
    {
        // https://symfony.com/doc/5.4/components/lock.html#doctrinedbalstore
        $connection = $this->entityManager->getConnection();

        return new DoctrineDbalStore($connection) ?: null;
    }
}
