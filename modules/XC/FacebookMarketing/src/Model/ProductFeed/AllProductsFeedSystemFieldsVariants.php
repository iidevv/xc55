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
 * @Extender\Depend({"XC\GoogleFeed", "XC\ProductVariants", "XC\SystemFields", "XC\FacebookMarketing"})
 */
class AllProductsFeedSystemFieldsVariants extends \XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed
{
    /**
     * @param \XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataGtin($entity, $fieldName)
    {
        return $entity->getDisplayUpcIsbn();
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataMpn($entity, $fieldName)
    {
        return $entity->getDisplayMnfVendor() ?: parent::getVariantDataMpn($entity, $fieldName);
    }
}
