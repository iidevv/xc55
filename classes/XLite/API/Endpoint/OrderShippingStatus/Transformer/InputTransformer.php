<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderShippingStatus\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\API\Endpoint\OrderShippingStatus\DTO\OrderShippingStatusInput as InputDTO;
use XLite\Model\Order;
use XLite\Model\Order\Status\Shipping as Model;

class InputTransformer implements DataTransformerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $orderID = $this->getOrderID($context['request_uri']);
        if (!$orderID) {
            throw new InvalidArgumentException('Order ID is invalid');
        }

        /** @var Order $order */
        $order = $this->getOrderRepository()->find($orderID);
        if (!$order) {
            throw new InvalidArgumentException(sprintf("Order with ID %d not found", $orderID));
        }

        $order->setShippingStatus($object->code);

        if (!$order->getShippingStatus()) {
            throw new InvalidArgumentException(sprintf("Status code '%s' is wrong", $object->code));
        }

        return $order->getShippingStatus();
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && ($context['input']['class'] ?? null) !== null;
    }

    protected function getOrderID(string $uri): ?int
    {
        if (!preg_match('/orders\/(\d+)\/shipping_status/Ss', $uri, $match)) {
            return null;
        }

        return (int)$match[1];
    }

    protected function getOrderRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Order::class);
    }
}
