<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentStatus\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\ItemDataProvider as ItemDataProviderParent;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\Model\Order;
use XLite\Model\Order\Status\Payment;

class ItemDataProvider extends ItemDataProviderParent
{
    protected EntityManagerInterface $entityManager;

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entitymanager): void
    {
        $this->entityManager = $entitymanager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return parent::supports($resourceClass, $operationName, $context)
            && $resourceClass === Payment::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var Order $order */
        $order = $this->getOrderRepository()->find($id['id']);
        if (!$order) {
            throw new InvalidArgumentException(sprintf('Order with ID %d not found', $id['id']));
        }
        $id['id'] = $order->getPaymentStatus()->getId();

        return parent::getItem($resourceClass, $id, $operationName, $context);
    }

    protected function getOrderRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Order::class);
    }
}
