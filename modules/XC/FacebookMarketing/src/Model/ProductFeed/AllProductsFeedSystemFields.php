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
 * @Extender\Depend("XC\SystemFields")
 */
class AllProductsFeedSystemFields extends \XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed
{
    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return array_merge(parent::getHeaders(), [
            [static::FIELD_PARAM_NAME => 'gtin'],
        ]);
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataGtin($entity, $fieldName)
    {
        return $entity->getUpcIsbn();
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataMpn($entity, $fieldName)
    {
        return $entity->getMnfVendor() ?: parent::getEntityDataMpn($entity, $fieldName);
    }
}
