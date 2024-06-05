<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderDetail\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\Model\OrderDetail;
use XLite\API\Traits\OrderBasedSubExtensionTrait;

class OrderBasedSubExtension implements CollectionSubExtensionInterface
{
    use OrderBasedSubExtensionTrait;

    /**
     * @var string[]
     */
    protected array $operationNames = ['get', 'get_cart_details'];

    public function support(string $className, string $operationName): bool
    {
        return $className === OrderDetail::class && in_array($operationName, $this->operationNames, true);
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        $orderID = $this->getOrderID($context);
        if (!$orderID) {
            throw new InvalidArgumentException('Order ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.order = :order_id', $rootAlias))
            ->setParameter('order_id', $orderID);

        $this->afterApplyToCollection($queryBuilder, $context);
    }

    protected function getOrderID(array $context): ?int
    {
        if (preg_match('/(orders|carts)\/(\d+)\/details/S', $context['request_uri'], $match)) {
            return (int) $match[2];
        }

        return null;
    }
}
