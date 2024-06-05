<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Traits;

use Doctrine\ORM\QueryBuilder;
use XLite\Model\Order;
use XLite\Model\Cart;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use XLite\Model\AEntity;

trait OrderBasedSubExtensionTrait
{
    protected function afterApplyToCollection(QueryBuilder $queryBuilder, array $context = []): void
    {
        $entity = $queryBuilder->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if (
            $entity &&
            !($this->isRelationWithOrderEntity($entity, $context) || $this->isRelationWithCartEntity($entity, $context))
        ) {
            throw new NotFoundHttpException('Not found');
        }
    }

    protected function isRelationWithOrderEntity(AEntity $entity, array $context = []): bool
    {
        return str_contains($context['request_uri'], 'orders')
            && $entity->getOrder() instanceof Order
            && !($entity->getOrder() instanceof Cart);
    }

    protected function isRelationWithCartEntity(AEntity $entity, array $context = []): bool
    {
        return str_contains($context['request_uri'], 'carts') && $entity->getOrder() instanceof Cart;
    }
}
