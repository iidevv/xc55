<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\ItemsList\Model\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping custom rates
 * @Extender\Mixin
 */
class Offline extends \XLite\View\ItemsList\Model\Shipping\Offline
{
    /**
     * Disable removing special methods
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return parent::isAllowEntityRemove($entity)
            && !$entity->getFree()
            && !$entity->isFixedFee();
    }

    /**
     * Check - switch entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntitySwitch(\XLite\Model\AEntity $entity)
    {
        return parent::isAllowEntitySwitch($entity)
            && !$entity->getFree()
            && !$entity->isFixedFee();
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isShowHandlingFee(\XLite\Model\AEntity $entity)
    {
        return parent::isShowHandlingFee($entity)
            && !$entity->getFree()
            && !$entity->isFixedFee();
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isShowTaxClass(\XLite\Model\AEntity $entity)
    {
        return parent::isShowTaxClass($entity)
            && !$entity->getFree()
            && !$entity->isFixedFee();
    }

    /**
     * Add right actions
     *
     * @return array
     */
    protected function getRightActions()
    {
        return array_merge(
            parent::getRightActions(),
            [
                'modules/XC/FreeShipping/items_list/model/table/shipping/carriers/free_shipping_tooltip.twig',
                'modules/XC/FreeShipping/items_list/model/table/shipping/carriers/shipping_freight_tooltip.twig',
            ]
        );
    }
}
