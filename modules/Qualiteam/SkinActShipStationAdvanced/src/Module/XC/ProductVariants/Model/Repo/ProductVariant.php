<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Module\XC\ProductVariants\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XC\ProductVariants\Model\ProductVariant as ProductVariantParent;

/**
 * @Extender\Mixin
 */
class ProductVariant extends \XC\ProductVariants\Model\Repo\ProductVariant
{
    public function prepareProductVariantToSyncShipStation(array $value): void
    {
        if ($value) {
            $queryBuilder = $this->createQueryBuilder();
            $queryBuilder->update(ProductVariantParent::class, 'pv')
                ->set('pv.prepareToSyncShipStation', ':trueToSync')
                ->setParameter('trueToSync', 1)
                ->andWhere('pv.id IN (' . implode(',', $value) . ')')
                ->execute();
        }
    }
}
