<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderHistory\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\Model\OrderHistoryEvents;
use XLite\API\Traits\OrderBasedSubExtensionTrait;

class OrderBasedSubExtension implements CollectionSubExtensionInterface
{
    use OrderBasedSubExtensionTrait;

    /**
     * @var string[]
     */
    protected array $operationNames = ['get', 'get_cart_history_events'];

    protected EntityManagerInterface $entityManager;

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    public function support(string $className, string $operationName): bool
    {
        return $className === OrderHistoryEvents::class && in_array($operationName, $this->operationNames, true);
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        $orderIDList = $this->getOrderIDList($context);
        if (!$orderIDList) {
            throw new InvalidArgumentException('Order ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        /** @var \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder */
        $queryBuilder->addInCondition(sprintf('%s.order', $rootAlias), $orderIDList);

        $this->afterApplyToCollection($queryBuilder, $context);
    }

    protected function getOrderIDList(array $context): array
    {
        if (preg_match('/(orders|carts)\/(\d+)\/history/S', $context['request_uri'], $match)) {
            return [(int) $match[2]];
        }

        return [];
    }
}
