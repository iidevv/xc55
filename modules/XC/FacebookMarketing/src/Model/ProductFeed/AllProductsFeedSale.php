<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Model\ProductFeed;

use XCart\Extender\Mapping\Extender;

/**
 * AllProductsFeed
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
class AllProductsFeedSale extends \XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed
{
    /**
     * @param \XLite\Model\AEntity $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataSalePrice($entity, $fieldName)
    {
        return ($entity->getDisplayPrice() < $entity->getDisplayPriceBeforeSale())
            ? $this->formatPrice($entity->getDisplayPrice()) . ' ' . \XLite::getInstance()->getCurrency()->getCode()
            : '';
    }

    /**
     * @param \XLite\Model\AEntity $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataPrice($entity, $fieldName)
    {
        return ($entity->getDisplayPrice() > $entity->getDisplayPriceBeforeSale())
            ? $this->formatPrice($entity->getDisplayPrice()) . ' ' . \XLite::getInstance()->getCurrency()->getCode()
            : $this->formatPrice($entity->getDisplayPriceBeforeSale()) . ' ' . \XLite::getInstance()->getCurrency()->getCode();
    }
}
