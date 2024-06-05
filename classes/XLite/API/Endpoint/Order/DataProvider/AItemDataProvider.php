<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\DataProvider;

abstract class AItemDataProvider extends \ApiPlatform\Core\Bridge\Doctrine\Orm\ItemDataProvider
{
    abstract protected function getEntityName(): string;

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return parent::supports($resourceClass, $operationName, $context)
            && $resourceClass === $this->getEntityName();
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if (is_array($id) && isset($id['id'])) {
            $id['order_id'] = $id['id'];
            unset($id['id']);
        }

        return parent::getItem($resourceClass, $id, $operationName, $context);
    }
}
