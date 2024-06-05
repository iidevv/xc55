<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\Category as CategoryTrack;

/**
 * Category list
 * @Extender\Mixin
 */
abstract class Category extends \XLite\View\ItemsList\Model\Category
{
    /**
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::removeEntity($entity);
        if ($result) {
            Mediator::getInstance()->addMessage(
                new CategoryTrack(
                    'Remove Category',
                    $entity
                )
            );
        }

        return $result;
    }
}
