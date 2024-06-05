<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\View\ItemsList\Model\Product\Admin;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\Product;

/**
 * Abstract admin-interface products list
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\ItemsList\Model\Product\Admin\AAdmin
{
    /**
     * @inheritdoc
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::removeEntity($entity);
        if ($result) {
            Mediator::getInstance()->addMessage(
                new Product(
                    'Remove Product',
                    $entity
                )
            );
        }

        return $result;
    }
}
