<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentStatus\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use XLite\Model\Order\Status\Payment;

class DataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Payment;
    }

    /**
     * @param Payment $data
     */
    public function persist($data, array $context = [])
    {
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
    }
}
