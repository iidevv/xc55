<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Symfony\Component\Lock;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\FlockStore;

class MainStoreFactory
{
    use LoggerAwareTrait;

    private $possibleStores;

    public function __construct(
        $possibleStores,
        LoggerInterface $logger
    ) {
        $this->possibleStores = $possibleStores;
        $this->setLogger($logger);
    }

    public function create(): PersistingStoreInterface
    {
        $store = null;
        foreach ($this->possibleStores ?? [] as $storeEngine) {
            $err = '';
            try {
                $store = $storeEngine->getStore();
            } catch (\Exception $e) {
                $err = $e->getMessage();
            }
            if (!empty($store) && empty($err)) {
                // success
                break;
            }

            $className = get_class($storeEngine);

            // Some store failed. Log and go to the next fallback in cycle
            $this->logger->warning("Symfony\Component\Lock\Store\\{$className} cannot be initialized. Choose another store or check arguments in config/services/store_for_symfony_lock.yaml.{$err}");
        }

        if (!$store) {
            // last chance to stay in turn
            $store = new FlockStore();
            $this->logger->warning("Fallback FlockStore is used. Check sub-services section in config/services/store_for_symfony_lock.yaml.");
        }

        return $store;
    }
}
